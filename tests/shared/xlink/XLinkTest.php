<?php
namespace Lib16\XML\Tests\Shared\XLink;

require_once 'vendor/autoload.php';

use Lib16\XML\Shared\XLink\ {
    XLink,
    XLinkConstants,
    Actuate,
    Show,
    Type
};
use Lib16\XML\Tests\ {
    XmlTestCase,
    Tml
};
use PHPUnit\Framework\TestCase;

class XLinkXml1 extends Tml
{

    const MORE_XML_NAMESPACES = [
        XLinkConstants::NAMESPACE_PREFIX => XLinkConstants::NAMESPACE
    ];

    use XLink;
}

class XLinkXml2 extends Tml
{
    use XLink;
}

class XLinkXml3 extends XLinkXml2
{
    const VERTICAL_ATTRIBUTES_ENABLED = true;
}

class XLinkTest extends TestCase
{
    public function testXLink()
    {
        $this->assertEquals(
            XmlTestCase::XML_DECL . '
<root xmlns:xlink="http://www.w3.org/1999/xlink"/>',
            XLinkXml1::createRoot('root')
        );
    }

    public function testSetXLinkNamespace()
    {
        $this->assertEquals(
            XmlTestCase::XML_DECL . '
<root xmlns:xlink="http://www.w3.org/1999/xlink"/>',
            XLinkXml2::createRoot('root')->setXLinkNamespace()
        );
    }

    public function testSetXLinkType1()
    {
        $this->assertEquals(
            '<e/>',
            XLinkXml1::c('e')->setXLinkType()
        );
    }

    public function testSetXLinkType2()
    {
        $this->assertEquals(
            '<e xlink:type="simple"/>',
            XLinkXml1::c('e')->setXLinkType(Type::SIMPLE())
        );
    }

    public function testSetXLinkHref1()
    {
        $this->assertEquals(
            '<e xlink:href="image.jpg"/>',
            XLinkXml1::c('e')->setXLinkHref('image.jpg')
        );
    }

    public function testSetXLinkHref2()
    {
        $this->assertEquals(
            '<e/>',
            XLinkXml1::c('e')->setXLinkHref(null)
        );
    }

    /**
     * @dataProvider setXLinkShowProvider
     */
    public function testSetXLinkShow(string $expected, Show $show = null)
    {
        $this->assertEquals(
            $expected,
            XLinkXml1::c('e')->setXLinkShow($show)
        );
    }

    public function setXLinkShowProvider(): array
    {
        return [
            ['<e xlink:show="embed"/>', Show::EMBED()],
            ['<e xlink:show="new"/>', Show::NEW()],
            ['<e xlink:show="replace"/>', Show::REPLACE()],
            ['<e xlink:show="other"/>', Show::OTHER()],
            ['<e xlink:show="none"/>', Show::NONE()],
            ['<e/>', null]
        ];
    }

    /**
     * @dataProvider setXLinkActuateProvider
     */
    public function testSetXLinkActuate(string $expected, Actuate $actuate = null)
    {
        $this->assertEquals(
            $expected,
            XLinkXml1::c('e')->setXLinkActuate($actuate)
        );
    }

    public function setXLinkActuateProvider(): array
    {
        return [
            ['<e xlink:actuate="onLoad"/>', Actuate::ONLOAD()],
            ['<e xlink:actuate="onRequest"/>', Actuate::ONREQUEST()],
            ['<e xlink:actuate="other"/>', Actuate::OTHER()],
            ['<e xlink:actuate="none"/>', Actuate::NONE()]
        ];
    }

    public function test()
    {
        // Example from https://de.wikipedia.org/wiki/XLink_(Syntax)
        $expected = "<element"
            . "\n\t\txmlns:xlink=\"http://www.w3.org/1999/xlink\""
            . "\n\t\txlink:href=\"user.xml\""
            . "\n\t\txlink:type=\"simple\""
            . "\n\t\txlink:role=\"http://www.example.com/list/userlist.xml\""
            . "\n\t\txlink:title=\"User List\">Current List of Users</element>";
        $actual = XLinkXml3::c('element', 'Current List of Users')
            ->setXLinkNamespace()
            ->setXLinkHref('user.xml')
            ->setXLinkType(Type::SIMPLE())
            ->setXLinkRole('http://www.example.com/list/userlist.xml')
            ->setXLinkTitle('User List')->__toString();
        $this->assertEquals($expected, $actual);

        // code-coverage
        $expected = '<e xlink:arcrole="http://www.example.com">';
        $actual = XLinkXml2::c('e')->setXLinkArcrole('http://www.example.com');
    }
}
