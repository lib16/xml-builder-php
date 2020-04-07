<?php
namespace Lib16\XML;

class XmlWrapper
{

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
}