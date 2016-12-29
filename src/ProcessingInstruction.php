<?php

namespace Lib16\XML;

class ProcessingInstruction
{
	protected $target;
	protected $content;
	protected $attributes;

	protected function __construct(string $target, string $content = null)
	{
		$this->target = $target;
		$this->content = $content;
	}

	public static function create(string $target, string $content = null): self
	{
		return new ProcessingInstruction($target, $content);
	}

	public function attrib(string $name, string $value = null): self
	{
		if ($this->attributes == null) {
			$this->attributes = new Attributes(null);
		}
		$this->attributes->set($name, $value);
		return $this;
	}

	public function getMarkup(): string
	{
		$markup = '<?' . $this->target;
		if (!is_null($this->content) && !empty($this->content)) {
			$markup .= ' ' . $this->content;
		}
		if (!is_null($this->attributes)) {
			$markup .= $this->attributes->getMarkup();
		}
		$markup .= ' ?>';
		return $markup;
	}

	public function __toString(): string
	{
		return $this->getMarkup();
	}
}
