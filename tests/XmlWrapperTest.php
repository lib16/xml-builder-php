<?php
namespace Lib16\XML\Tests;

require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Lib16\XML\XmlWrapper;

class Html extends XmlWrapper
{
    public static function create(): self
    {
        return new Html(Hml::createRoot('html'));
    }

    public function body(string ...$text): Body
    {
        return new Body($this->xml->append('body')->appendText(...$text));
    }
}

class Body extends XmlWrapper
{

}

class XmlWrapperTest extends TestCase
{
    public function test()
    {
        $body = Html::create()->body();
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
