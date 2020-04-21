<?php
namespace Lib16\XML\Tests\Shared;

use Lib16\Utils\Enums\CSS\Media;
use Lib16\Utils\Enums\Mime\StyleType;
use Lib16\XML\Shared\XmlStylesheet;
use Lib16\XML\Tests\Tml;
use const Lib16\XML\Tests\XML_DECL;
use PHPUnit\Framework\TestCase;

class StyledXml extends Tml
{
    use XmlStylesheet;
}

class XmlStylesheetTest extends TestCase
{
    public function test1() {
        $expected = XML_DECL . '
<?xml-stylesheet href="style.css" ?>
<styled/>';
        $xml = StyledXml::createRoot('styled');
        $xml->xmlStylesheet('style.css');
        $actual = $xml;
        $this->assertEquals($expected, $actual);
    }

    public function test2() {
        $expected = XML_DECL . '
<?xml-stylesheet href="one.css" type="text/css" media="screen,print" alternate="yes" title="One" charset="UTF-8" ?>
<?xml-stylesheet href="two.css" type="text/css" media="screen,print" alternate="yes" title="Two" charset="UTF-8" ?>
<styled/>';
        $xml = StyledXml::createRoot('styled');
        $xml->xmlStylesheet(
            'one.css',
            true,
            'One',
            StyleType::CSS(),
            'UTF-8',
            Media::SCREEN(),
            Media::PRINT()
        );
        $xml->xmlStylesheet('two.css')
            ->setMedia(Media::SCREEN(), Media::PRINT())
            ->setType(StyleType::CSS())
            ->setAlternate()
            ->setTitle('Two')
            ->setCharset('UTF-8');
        $actual = $xml;
        $this->assertEquals($expected, $actual);
    }
}