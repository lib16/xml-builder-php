<?php
namespace Lib16\XML\Shared;

use Lib16\Utils\Enums\CSS\Media;
use Lib16\Utils\Enums\Mime\StyleType;
use Lib16\XML\ProcessingInstruction;

class XmlStylesheetInstruction extends ProcessingInstruction
{
    use MediaAttribute, TitleAttribute;

    public static function createXmlStylesheetInstruction(
        string $href,
        bool $alternate = false,
        string $title = null,
        StyleType $type = null,
        string $charset = null,
        Media ...$media
    ): self {
        return (new XmlStylesheetInstruction('xml-stylesheet'))
            ->attrib('href', $href)
            ->setType($type)
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

    public function setType(StyleType $type = null)
    {
        return $this->attrib('type', $type);
    }
}