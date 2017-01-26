<?php

namespace Lib16\XML\Shared;

/**
 * <code>xml:lang</code>, <code>xml:space</code>, <code>xml:base</code> and <code>xml:id</code>.
 */
trait XmlAttributes
{
	/**
	 * @param  string|null  lang  A BCP 47 language tag. For example "en" or "fr-CA".
	 */
	public function setLang(string $lang): self
	{
		$this->attributes->set('xml:lang', $lang);
		return $this;
	}

	public function setSpace(Space $space): self
	{
		$this->attributes->set('xml:space', $space);
		return $this;
	}

	public function setBase(string $base): self
	{
		$this->attributes->set('xml:base', $base);
		return $this;
	}

	public function setId(string $id): self
	{
		$this->attributes->set('xml:id', $id);
		return $this;
	}
}