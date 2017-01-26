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

	const CHARACTER_ENCODING = 'UTF-8';
	const LINE_BREAK = "\n";
	const INDENTATION = "\t";

	protected $name;
	protected $content;
	protected $attributes;
	protected $instructions;
	protected $cdata;

	protected $children;
	protected $root;
	protected $parent;

	protected $options;
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
		$class = get_called_class();
		$root = new $class($name);
		$root->setXmlns();
		return $root;
	}

	public static function createSub(string $name = null, $content = null): self
	{
		$class = get_called_class();
		$element = new $class($name, $content);
		$element->sub = true;
		return $element;
	}

	/**
	 * Adds child elements.
	 *
	 * @param  string       $name
	 * @param  string|null  ...$content
	 *
	 * @return The first appended element.
	 */
	public function append(string $name = null, ...$content): self
	{
		$class = get_called_class();
		if (!count($content)) {
			$element = new $class($name, null, $this->root, $this);
			$this->children[] = $element;
			return $element;
		}
		$index = count($this->children);
		foreach ($content as $content) {
			$this->children[] = new $class($name, $content, $this->root, $this);
		}
		return $this->children[$index];
	}

	/**
	 * Appends a text line.
	 */
	public function appendText(string $text): self
	{
		return $this->append(null, $text);
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

	public function disableLineBreak(bool $lineBreakDisabled = true): self
	{
		return $this->setOption('lineBreakDisabled', $lineBreakDisabled);
	}

	public function disableIndentation(bool $indentationDisabled = true): self
	{
		return $this->setOption('indentationDisabled', $indentationDisabled);
	}

	public function disableTextIndentation(bool $textIndentationDisabled = true): self
	{
		return $this->setOption('textIndentationDisabled', $textIndentationDisabled);
	}

	public function attrib(string $name, $value = true): self
	{
		$this->attributes->set($name, $value);
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
		return $this->buildMarkup(null);
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
		return 'Content-Type: ' . static::MIME_TYPE . '; charset=' . static::CHARACTER_ENCODING;
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

	private function setOption(string $option, bool $value): self
	{
		$this->options = $this->options ?? new \stdClass();
		$this->options->$option = $value;
		return $this;
	}

	private function buildMarkup(\stdClass $v = null): string
	{
		$v = $this->calculateVars($v);

		$markup = '';
		if ($this->isRoot() && !$this->sub) {
			$markup .= $this->head($v);
		}
		if (!empty($this->children)) {
			$markup .= $this->container($v);
		}
		else {
			$markup .= $this->element($v);
		}
		return $markup;
	}

	private function head(\stdClass $v): string
	{
		$markup = '';
		if (static::XML_DECLARATION_ENABLED) {
			$markup .= ProcessingInstruction::create('xml')
					->attrib('version', static::XML_VERSION)
					->attrib('encoding', static::CHARACTER_ENCODING). $v->lineBr;
		}
		if (!is_null($this->instructions)) {
			foreach ($this->instructions as $instruction) {
				$markup .= $instruction . $v->lineBr;
			}
		}
		if (!empty(static::DOCTYPE)) {
			$markup .= static::DOCTYPE . $v->lineBr;
		}
		return $markup;
	}

	private function container(\stdClass $v): string
	{
		$markup = '';
		if ($v->hasTags) {
			$markup .= $v->indentation . $this->openingTag($v) . $v->lineBr;
		}
		if ($this->cdata) {
			$markup .= $v->indentation . self::CDATA_START . $v->lineBr;
		}
		if (!empty($v->content)) {
			$markup .= $v->newIndentation . $v->content . $v->lineBr;
		}
		foreach ($this->children as $child) {
			$markup .= $child->buildMarkup($v) . $v->lineBr;
		}
		if ($this->cdata) {
			$markup .= $v->indentation . self::CDATA_STOP . $v->lineBr;
		}
		if ($v->hasTags) {
			$markup .= $v->indentation . $this->closingTag($v) . $v->lineBr;
		}
		return rtrim($markup, $v->lineBr);
	}

	private function element(\stdClass $v): string
	{
		$markup = '';
		$hasContent = static::HTML_MODE_ENABLED ? $v->content !== null : $v->content != '';
		if ($v->hasTags) {
			if ($hasContent) {
				if ($this->cdata) {
					$v->content = self::CDATA_START . $v->content . self::CDATA_STOP;
				}
				$markup .= $v->indentation .
						$this->openingTag($v) . $v->content . $this->closingTag($v);
			}
			else {
				$markup .= $v->indentation . $this->standaloneTag($v);
			}
		}
		else if ($hasContent) {
			$markup .= $v->indentation . $v->content;
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

	private function calculateVars(\stdClass $vars = null)
	{
		$v = new \stdClass();

		$v->indentation = $vars->newIndentation ?? '';
		$v->newIndentation = $v->indentation;

		$v->lineBr = isset($this->options->lineBreakDisabled)
				? ($this->options->lineBreakDisabled ? '' : static::LINE_BREAK)
				: ($vars->lineBr ?? static::LINE_BREAK);
		$v->indent = isset($this->options->indentationDisabled)
				? ($this->options->indentationDisabled ? '' : static::INDENTATION)
				: ($vars->indent ?? static::INDENTATION);
		$v->textIndentDisabled = isset($this->options->textIndentationDisabled)
				? $this->options->textIndentationDisabled
				: ($vars->textIndentDisabled ?? false);

		$indent = $v->lineBr == '' ? '' : $v->indent;

		$v->content = !empty($this->content) && $v->lineBr != '' && !$v->textIndentDisabled
				? str_replace($v->lineBr, $v->lineBr . $v->indentation, $this->content)
				: $this->content;
		$v->hasTags = !empty($this->name);
		if ($v->hasTags) {
			$v->newIndentation .= $indent;
			$v->attributes = $this->attributes->getMarkup(static::HTML_MODE_ENABLED,
					static::VERTICAL_ATTRIBUTES_ENABLED && $v->lineBr != ''
					? $v->lineBr . $v->indentation . $indent : ' ');
			$v->name = empty(static::NAMESPACE_PREFIX)
					? $this->name : static::NAMESPACE_PREFIX . ':' . $this->name;
		}

		return $v;
	}
}