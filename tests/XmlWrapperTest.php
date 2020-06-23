<?php
namespace Lib16\XML\Tests;

use PHPUnit\Framework\TestCase;
use Lib16\XML\XmlWrapper;

class XmlWrapperTest extends TestCase
{
    public function test()
    {
        $body = _Html::create()->body();
        $this->assertEquals(
            "<html>\n\t<body>\n\t</body>\n</html>",
            $body->__toString()
        );
        $this->assertEquals(
            $body->__toString(),
            $body->getXml()->__toString()
        );
        $this->assertEquals(
            "<body>\n</body>",
            $body->getMarkup()
        );
    }
}

class _Html extends XmlWrapper
{
    public static function create(): self
    {
        return new _Html(Hml::createRoot('html'));
    }

    public function body(string ...$text): _Body
    {
        return new _Body($this->xml->append('body')->appendText(...$text));
    }
}

class _Body extends XmlWrapper
{

}

