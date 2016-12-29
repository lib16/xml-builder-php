<?php

namespace Lib16\XML\Shared;

Use Lib16\Utils\Enums\Media;

trait XmlStylesheet
{
	public function xmlStylesheet(string $href,
			bool $alternate = false,
			string $title = null,
			$setType = false,
			$charset = null,
			Media ...$media): XmlStylesheetInstruction
	{
		return $this->instructions[] = XmlStylesheetInstruction::createXmlStylesheetInstruction(
				$href, $alternate, $title, $setType, $charset, ...$media);
	}
}