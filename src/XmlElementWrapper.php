<?php
namespace Lib16\XML;

class XmlElementWrapper
{
    const NAME = null;

    const END_TAG_OMISSION = true;

    protected $xml;

    protected function __construct(Xml $xml)
    {
        $this->xml = $xml;
    }

    public function getXml(): Xml
    {
        return $this->xml;
    }

    public function getMarkup(): string
    {
        return $this->xml->getMarkup();
    }

    public function __toString(): string
    {
        return $this->xml->__toString();
    }

    public function attrib(string $name, $value): self
    {
        $this->xml->attrib($name, $value);
        return $this;
    }

    public function append(string $name, string $content = null): self
    {
        if ($content !== null) {
            $this->xml->append($name, $content);
        }
        return $this;
    }

    public function appendDateTime(
        string $name,
        \DateTime $datetime = null,
        string $format = \DateTime::RSS
    ): self {
        if ($datetime !== null) {
            $this->xml->append($name, $datetime->format($format));
        }
        return $this;
    }

    public static function appendTo(
        self $parent,
        string $content = null
    ): self {
        if (!$content && !static::END_TAG_OMISSION) {
            $content = '';
        }
        return new static($parent->getXml()->append(static::NAME, $content));
    }
}