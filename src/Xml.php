<?php

namespace Lib16\XML;

/**
 * Simplifies respectively unifies the creation of XML documents.
 */
class Xml
{
	const CDATA_START = '<![CDATA[';
	const CDATA_STOP = ']]>';

	const HTML_MODE_ENABLED = false;
	const DOCTYPE = null;
	const MIME_TYPE = 'application/xml';
	const FILENAME_EXTENSION = 'xml';
	const XML_VERSION = '1.0';
	const XML_NAMESPACE = null;
	const MORE_XML_NAMESPACES = [];
	const NAMESPACE_PREFIX = '';
	const XML_DECLARATION_ENABLED = true;
	const VERTICAL_ATTRIBUTES_ENABLED = false;

	protected static $characterEncoding = 'UTF-8';
	protected static $lineBreak = "\n";
	protected static $indentation = "\t";

	protected $name;
	protected $content;
	protected $attributes;
	protected $instructions;
	protected $cdata;

	protected $children;
	protected $root;
	protected $parent;

	protected $flags;
	protected $sub;

	/**
	 * The constructor is used internally to create child elements.
	 */
	protected function __construct(
			string $name = null,
			string $content = null,
			Xml $root = null,
			Xml $parent = null)
	{
		$this->name = $name;
		$this->content = $content;
		$this->attributes = new Attributes();
		$this->root = $root ?? $this;
		$this->parent = $parent;
		$this->sub = false;
	}

	public static function createRoot(string $name = null): self
	{
		$root = new static($name);
		$root->setXmlns();
		return $root;
	}

	public static function create(string $name = null, $content = null): self
	{
		$element = new static($name, $content);
		$element->sub = true;
		return $element;
	}

	/**
	 * @deprecated You better use the <code>create</code> method instead.
	 */
	public static function createSub(string $name = null, $content = null): self
	{
		return static::create($name, $content);
	}

	public static function setCharacterEncoding(string $encoding)
	{
		self::$characterEncoding = $encoding;
	}

	public static function setLineBreak(string $lineBreak)
	{
		self::$lineBreak = $lineBreak;
	}

	public static function setIndentation(string $indentation)
	{
		self::$indentation = $indentation;
	}

	/**
	 * Adds child elements.
	 *
	 * @return  self  The first appended element.
	 */
	public function append(string $name = null, ...$content): self
	{
		if (!empty($this->content)) {
			$this->children[] = new static(null, $this->content, $this->root, $this);
			$this->content = null;
		}
		if (!count($content)) {
			$element = new static($name, null, $this->root, $this);
			$this->children[] = $element;
			return $element;
		}
		$index = count($this->children);
		foreach ($content as $content) {
			$this->children[] = new static($name, $content, $this->root, $this);
		}
		return $this->children[$index];
	}

	/**
	 * @return  self  The current element (not the appended).
	 */
	public function appendLeaf(string $name, $content = null): self
	{
		if (!empty($name) && !empty($content)) {
			$this->append($name, $content);
		}
		return $this;
	}

	/**
	 * Appends a text line.
	 */
	public function appendText(string ...$text): self
	{
		$this->append(null, ...$text);
		return $this;
	}

	/**
	 * Appends a comment.
	 */
	public function comment(string $content): self
	{
		return $this->appendText('<!-- ' . $content . ' -->');
	}

	/**
	 * Appends a previously created subtree.
	 *
	 * @param  Xml  $element  Root element of the subtree.
	 */
	public function inject(Xml $element): self
	{
		$element->root = $this->root;
		$element->parent = $this;
		$this->children[] = $element;
		return $element;
	}

	public function getRoot(): self
	{
		return $this->root->isRoot() ? $this->root : $this->root->getRoot();
	}

	/**
	 * @param  level     Number of recursions (>=2).
	 * @return Xml|null  Returns null for root elements.
	 */
	public function getParent(int $level = 1)
	{
		return ($level > 1 && !is_null($this->parent))
				? $this->parent->getParent(--$level)
				: $this->parent;
	}

	public function countChildElements(): int
	{
		return count($this->children);
	}

	public function getChild(int $index): self
	{
		return $this->children[$index];
	}

	public function getContent(): string
	{
		return $this->content;
	}

	/**
	 * Subordinated content and elements will be inserted into a CDATA section.
	 */
	public function cdata(): self
	{
		$this->cdata = true;
		return $this;
	}

	/**
	 * Sets the <code>xmlns</code> attribute.
	 */
	public function setXmlns(string $uri = null, string $prefix = null): self
	{
		if (is_null($uri)) {
			if (!is_null(static::XML_NAMESPACE)) {
				$this->setXmlns(static::XML_NAMESPACE);
			}
			foreach (static::MORE_XML_NAMESPACES as $prefix => $uri) {
				$this->setXmlns($uri, $prefix);
			}
		}
		else {
			$this->attributes->set(!empty($prefix) ? 'xmlns:' . $prefix : 'xmlns', $uri);
		}
		return $this;
	}

	public function addProcessingInstruction(
			string $target, string $content = null): ProcessingInstruction
	{
		$instr = ProcessingInstruction::create($target, $content);
		if (is_null($this->instructions)) {
			$this->instructions = [];
		}
		$this->instructions[] = $instr;
		return $instr;
	}

	public function disableLineBreak(): self
	{
		return $this->setFlag('lineBreakDisabled');
	}

	public function disableTextIndentation(): self
	{
		return $this->setFlag('textIndentationDisabled');
	}

	public function attrib(string $name, $value = true): self
	{
		$this->attributes->set($name, $value);
		return $this;
	}

	public function boolAttrib(string $name, $value, string $comparisonAttribute = null): self
	{
		if (!is_bool($value) && $this->attributes->get($comparisonAttribute) === false) {
			foreach ($this->children as $child) {
				$child->boolAttrib($name, $value, $comparisonAttribute);
			}
			return $this;
		}
		$this->attributes->setBoolean($name, $value, $comparisonAttribute);
		return $this;
	}

	public function getAttributes(): Attributes
	{
		return $this->attributes;
	}

	/**
	 * Sets attribute list.
	 *
	 * @see Attributes#setAttributes(Attributes)
	 */
	public function setAttributes(Attributes $attributes): self
	{
		$this->attributes->setAttributes($attributes);
		return $this;
	}

	public function getMarkup(): string
	{
		return ltrim($this->buildMarkup(null), self::$lineBreak);
	}

	/**
	 * Shorthand method for <code>->getRoot()->getMarkup()</code>.
	 */
	public function __toString(): string
	{
		return $this->root->getMarkup();
	}

	/**
	 * Creates a string for <code>Content-Disposition</code> header field.
	 */
	public static function getContentDispositionHeaderfield(string $filename): string
	{
		if (static::FILENAME_EXTENSION) {
			$filename .= '.' . static::FILENAME_EXTENSION;
		}
		return 'Content-Disposition: attachment; filename="' . $filename . '"';
	}

	/**
	 * Creates a string for the <code>Content-Type</code> header field.
	 */
	public static function getContentTypeHeaderfield()
	{
		return 'Content-Type: ' . static::MIME_TYPE . '; charset=' . self::$characterEncoding;
	}

	public static function headerfields(string $filename = null)
	{
		if ($filename) {
			header(self::getContentDispositionHeaderfield($filename));
		}
		header(self::getContentTypeHeaderfield());
	}

	private function isRoot(): bool
	{
		return $this->root == $this;
	}

	private function setFlag(string $flagName): self
	{
		$this->flags = $this->flags ?? [];
		$this->flags[$flagName] = true;
		return $this;
	}

	private function buildMarkup(\stdClass $v = null): string
	{
		$v = $this->calculateVars($v);

		$markup = '';
		if ($this->isRoot() && !$this->sub) {
			$markup .= $this->buildHead($v);
		}
		if (!empty($this->children)) {
			$markup .= $this->buildContainer($v);
		}
		else {
			$markup .= $this->buildElement($v);
		}
		return $markup;
	}

	private function buildHead(\stdClass $v): string
	{
		$markup = '';
		if (static::XML_DECLARATION_ENABLED) {
			$markup .= $v->whitespace . ProcessingInstruction::create('xml')
					->attrib('version', static::XML_VERSION)
					->attrib('encoding', self::$characterEncoding);
		}
		if (!is_null($this->instructions)) {
			foreach ($this->instructions as $instruction) {
				$markup .= $v->whitespace . $instruction;
			}
		}
		if (!empty(static::DOCTYPE)) {
			$markup .= $v->whitespace . static::DOCTYPE;
		}
		return $markup;
	}

	private function buildContainer(\stdClass $v): string
	{
		$markup = '';
		if ($v->hasTags) {
			$markup .= $v->whitespace . $this->openingTag($v);
		}
		if ($this->cdata) {
			$markup .= $v->whitespaceCData . self::CDATA_START;
		}
		foreach ($this->children as $child) {
			$markup .= $child->buildMarkup($v);
		}
		if ($this->cdata) {
			$markup .= $v->whitespaceCData . self::CDATA_STOP;
		}
		if ($v->hasTags) {
			$markup .= $v->whitespaceContainerEnd . $this->closingTag($v);
		}
		return $markup;
	}

	private function buildElement(\stdClass $v): string
	{
		$markup = '';
		$hasContent = static::HTML_MODE_ENABLED ? $v->content !== null : $v->content != '';
		if ($v->hasTags) {
			if ($hasContent) {
				if ($this->cdata) {
					$v->content = self::CDATA_START . $v->content . self::CDATA_STOP;
				}
				$markup .= $v->whitespace .
				$this->openingTag($v) . $v->content . $this->closingTag($v);
			}
			else {
				$markup .= $v->whitespace . $this->standaloneTag($v);
			}
		}
		else if ($hasContent) {
			$markup .= $v->whitespace . $v->content;
		}
		return $markup;
	}

	private function openingTag(\stdClass $v): string
	{
		return '<' . $v->name . $v->attributes . '>';
	}

	private function closingTag(\stdClass $v): string
	{
		return '</' . $v->name . '>';
	}

	private function standaloneTag(\stdClass $v): string
	{
		return '<' . $v->name . $v->attributes . (static::HTML_MODE_ENABLED ? '>' : '/>');
	}

	private function calculateVars(\stdClass $parentVars = null)
	{
		$vars = new \stdClass();

		if (isset($this->flags['textIndentationDisabled'])) {
			$vars->textIndentDisabled = true;
		}
		else if (isset($parentVars)) {
			$vars->textIndentDisabled = $parentVars->textIndentDisabled;
		}
		else {
			$vars->textIndentDisabled = false;
		}

		if (isset($parentVars)) {
			if (isset($parentVars->lineBreakDisabled)) {
				$vars->whitespace = '';
			}
			else {
				$vars->whitespace = $parentVars->whitespace;
				if ($this->parent->name != '' && $vars->whitespace != '') {
					$vars->whitespace .= self::$indentation;
				}
			}
		}
		else {
			$vars->whitespace = self::$lineBreak;
		}

		if (isset($this->flags['lineBreakDisabled'])) {
			$vars->lineBreakDisabled = true;
			$vars->whitespaceContainerEnd = '';
			$vars->whitespaceCData = '';
		}
		else {
			$vars->whitespaceContainerEnd = $vars->whitespace;
			$vars->whitespaceCData = $vars->whitespace;
		}

		if (!empty($this->content) && $vars->whitespace != '' && !$vars->textIndentDisabled) {
			$vars->content = str_replace(self::$lineBreak, $vars->whitespace, $this->content);
		}
		else {
			$vars->content = $this->content;
		}

		$vars->hasTags = !empty($this->name);
		if ($vars->hasTags) {
			$vars->attributes = $this->attributes->getMarkup(static::HTML_MODE_ENABLED,
					static::VERTICAL_ATTRIBUTES_ENABLED && $vars->whitespace != ''
							? $vars->whitespace . self::$indentation . self::$indentation
							: ' ');
			$vars->name = empty(static::NAMESPACE_PREFIX)
					? $this->name
					: static::NAMESPACE_PREFIX . ':' . $this->name;
		}
		return $vars;
	}
}