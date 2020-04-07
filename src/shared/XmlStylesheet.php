<?php
namespace Lib16\XML\Shared;

Use Lib16\Utils\Enums\CSS\Media;
Use Lib16\Utils\Enums\Mime\StyleType;

trait XmlStylesheet
{

    public function xmlStylesheet(
        string $href,
        bool $alternate = false,
        string $title = null,
        StyleType $type = null,
        string $charset = null,
        Media ...$media
    ): XmlStylesheetInstruction {
        return $this->instructions[] = XmlStylesheetInstruction::createXmlStylesheetInstruction(
            $href,
            $alternate,
            $title,
            $type,
            $charset,
            ...$media
        );
    }
}