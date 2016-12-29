<?php

namespace Lib16\XML\Shared;

use Lib16\Utils\Enums\Media;
use Lib16\XML\ProcessingInstruction;

class XmlStylesheetInstruction extends ProcessingInstruction
{
	use MediaAttribute, TitleAttribute;

	public static function createXmlStylesheetInstruction(string $href,
			bool $alternate = false,
			string $title = null,
			bool $setType = false,
			string $charset = null,
			Media ...$media): self
	{
		return (new XmlStylesheetInstruction('xml-stylesheet'))
				->attrib('href', $href)
				->setType($setType)
				->setMedia(...$media)
				->setAlternate($alternate)
				->setTitle($title)
				->setCharset($charset);
	}

	public function setAlternate(bool $alternate = true): self
	{
		return $this->attrib('alternate', $alternate ? 'yes' : null);
	}

	public function setCharset(string $charset = null): self
	{
		return $this->attrib('charset', $charset);
	}

	public function setType(bool $setType = true)
	{
		return $this->attrib('type', $setType ? 'text/css' : null);
	}
}