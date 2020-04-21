<?php
namespace Lib16\XML\Tests;

use Lib16\Utils\Enums\CSS\Media;
use Lib16\XML\ {
    Xml,
    Attributes
};
use PHPUnit\Framework\TestCase;

const MULTILINE = "lorem\nipsum\ndolor\nsit";

class XmlTest extends TestCase
{
    /**
     * @dataProvider appendProvider
     */
    public function testAppend(
        string $expected,
        string $expectedHtmlMode,
        string $name = null,
        ...$content
    ) {
        $actual = Tml::c()->append($name, ...$content);
        $actualHtmlMode = Hml::c()->append($name, ...$content);
        $this->assertEquals($expected, $actual);
        $this->assertEquals($expectedHtmlMode, $actualHtmlMode);
    }

    public function appendProvider(): array
    {
        return [
            ['', '', null],
            ['', '', ''],
            ['content', 'content', null, 'content'],
            ['content', 'content', '', 'content'],
            ['<e/>', '<e>', 'e'],
            ['<e/>', '<e></e>', 'e', ''],
            ['<e>content</e>', '<e>content</e>', 'e', 'content']
        ];
    }

    public function testAppendMultipleContent()
    {
        $this->assertEquals(
            "<e foo=\"bar\">lorem</e>\n<e>ipsum</e>\n<e>dolor</e>",
            Tml::c()
                ->append('e', 'lorem', 'ipsum', 'dolor')
                ->setAttributes((new Attributes())->set('foo', 'bar'))
        );
    }

    public function testAppendIndentation1()
    {
        $this->assertEquals('<e/>', Tml::c()->append('')->append('e'));
    }

    public function testAppendIndentation2()
    {
        $expected = "content 1\n"
            . "<e>content 2</e>\n"
            . "content 3\n"
            . "<e>content 4</e>\n"
            . "<e>content 5</e>";
        $xml = Tml::c();
        $xml->append(null, 'content 1');
        $xml->append('e', 'content 2');
        $xml->append(null, 'content 3');
        $xml->append('e', 'content 4');
        $xml->append('e', 'content 5');
        $actual = $xml;
        $this->assertEquals($expected, $actual);
    }

    public function testAppendIndentation3()
    {
        $expected = "<e>\n\t<e>1.1</e>\n</e>\n<e>\n\t<e>2.1</e>\n\t<e>2.2</e>\n</e>";
        $xml = Tml::c();
        $xml->append('e')->append('e', '1.1');
        $xml->append('e')
            ->append('e', '2.1')
            ->getParent()
            ->append('e', '2.2');
        $actual = $xml;
        $this->assertEquals($expected, $actual);
    }

    public function testAppendIndentation4()
    {
        $this->assertEquals(
            "<e>\n\t1\n\t<e>1.1</e>\n\t<e>1.2</e>\n</e>",
            Tml::c()
                ->append('e', '1')
                ->append('e', '1.1')
                ->getParent()
                ->append('e', '1.2')
        );
    }

    public function testAppendText1()
    {
        $this->assertEquals(
            "<e/>\n<!-- comment -->\n<f>\n\tcontent 1\n\tcontent 2\n</f>",
            Tml::c()->append('e')
                ->getParent()
                ->comment('comment')
                ->append('f', 'content 1')
                ->appendText('content 2')
        );
    }

    public function testAppendText2()
    {
        $this->assertEquals(
            "<e>\n\tcontent\n</e>",
            Tml::c()->append('e', '')->appendText('content')
        );
    }

    public function testAppendText3()
    {
        $this->assertEquals(
            "<e>\n\tone\n\ttwo\n</e>",
            Tml::c('e')->appendText('one')->appendText('two')
        );
    }

    public function testAppendText4()
    {
        $this->assertEquals(
            "<e>\n\tone\n\ttwo\n</e>",
            Tml::c('e')->appendText('one', 'two')
        );
    }

    public function testAppendText5()
    {
        $expected = "<section>\n\t<div>\n"
            . "\t\t<p>lorem\n\t\tipsum\n\t\tdolor\n\t\tsit</p>\n"
            . "\t\t<p>\n\t\t\tlorem\n\t\t\tipsum\n\t\t\tdolor\n\t\t\tsit\n\t\t</p>\n"
            . "\t</div>\n</section>";
        $xml = Tml::c('section')->append('div');
        $xml->append('p', MULTILINE);
        $xml->append('p')->appendText(MULTILINE);
        $actual = $xml;
        $this->assertEquals($expected, $actual);
    }

    public function testAppendLeaf()
    {
        $this->assertEquals(
            "<e>\n\t<f>content</f>\n</e>",
            Tml::c('e')->appendLeaf('f', 'content')->appendLeaf('g')->__toString()
        );
    }

    public function testInject1()
    {
        $this->assertEquals(
            "<e a>\n\t<f xmlns=\""
                . Nml::XML_NAMESPACE
                . "\">\n\t\t<g/>\n\t</f>\n</e>",
            Hml::createRoot('e')
                ->setAttributes((new Attributes())->set('a', true))
                ->inject(Nml::createRoot('f')->append('g')->getRoot())
        );
    }

    public function testInject2()
    {
        $this->assertEquals(
            "<e a>\n\t<f/>\n</e>",
            Hml::createRoot('e')
                ->setAttributes((new Attributes())->set('a', true))
                ->inject(Tml::c()->append('f')->getRoot())
        );
    }

    public function testGetChild()
    {
        $expected = '<c>two</c>';
        $xml = Tml::c('p');
        $xml->append('c', 'one', 'two', 'three');
        $actual = $xml->getChild(1)->getMarkup();
        $this->assertEquals($expected, $actual);
    }

    public function testCountChildElements()
    {
        $xml = Tml::c('p');
        $xml->append('c', 'one', 'two', 'three');
        $this->assertEquals(3, $xml->countChildElements());
    }

    public function testGetParent1()
    {
        $this->assertEquals(
            "<f>\n\t<g/>\n</f>",
            Tml::c('e')->append('f')->append('g')->getParent()->getMarkup()
        );
    }

    public function testGetParent2()
    {
        $this->assertEquals(
            "<f>\n\t<g>\n\t\t<h/>\n\t</g>\n</f>",
            Tml::c('e')
                ->append('f')
                ->append('g')
                ->append('h')
                ->getParent(2)
                ->getMarkup()
        );
    }

    public function testGetParent3()
    {
        $this->assertEquals(
            "<e>\n\t<f>\n\t\t<g>\n\t\t\t<h/>\n\t\t</g>\n\t</f>\n</e>",
            Tml::c('e')
                ->append('f')
                ->append('g')
                ->append('h')
                ->getParent(3)
                ->getMarkup()
        );
    }

    public function testGetParent4()
    {
        $this->assertEquals(
            null,
            Tml::c('e')->append('f')->append('g')->append('h')->getParent(10)
        );
    }

    public function testGetParent5()
    {
        $this->assertEquals(
            "<e>\n\t<f>\n\t\t<g>\n\t\t\t<h/>\n\t\t</g>\n\t</f>\n</e>",
            Tml::c('e')->append('f')
                ->inject(Tml::c('g')->append('h')->getParent())
                ->getParent(2)
                ->getMarkup()
        );
    }

    public function testGetRoot1()
    {
        $this->assertEquals(
            "<e>\n\t<f>\n\t\t<g>\n\t\t\t<h/>\n\t\t</g>\n\t</f>\n</e>",
            Tml::c('e')
                ->append('f')
                ->append('g')
                ->append('h')
                ->getRoot()
                ->getMarkup()
        );
    }

    public function testGetRoot2()
    {
        $expected = "<r1>\n\t<e1>\n\t\t<r2>\n\t\t\t<e2>\n\t\t</r2>\n\t</e1>\n</r1>";
        $e1 = Tml::c('r1')->append('e1');
        $e2 = Hml::c('r2')->append('e2');
        $e1->inject($e2->getRoot());
        $actual = $e2->getRoot()->getMarkup();
        $this->assertEquals($expected, $actual);
    }

    public function testCdata1()
    {
        $this->assertEquals('<e/>', Tml::c()->append('e')->cdata());
    }

    public function testCdata2()
    {
        $this->assertEquals(
            '<e><![CDATA[content]]></e>',
            Tml::c()->append('e', 'content')->cdata()
        );
    }

    public function testCdata3()
    {
        $this->assertEquals(
            "<e>\n<![CDATA[\n\t<f>content</f>\n]]>\n</e>",
            Tml::c()->append('e')->cdata()->append('f', 'content')
        );
    }

    public function testSetXmlns1()
    {
        $this->assertEquals(
            "<r xmlns:foo=\"http://example->com/foo\"/>",
            Tml::c('r')->setXmlns('http://example->com/foo', 'foo')
        );
    }

    public function testSetXmlns2()
    {
        $this->assertEquals(
            "<r xmlns=\"http://example->com/foo\"/>",
            Tml::c('r')->setXmlns('http://example->com/foo', '')
        );
    }

    public function testSetXmlns3()
    {
        $this->assertEquals(
            "<r xmlns=\"http://example->com/foo\"/>",
            Tml::c('r')->setXmlns('http://example->com/foo')
        );
    }

    public function testSetXmlns4()
    {
        $this->assertEquals('<r/>', Tml::c('r')->setXmlns());
    }

    public function testSetXmlns5() {
        $this->assertEquals(
            '<r xmlns="' . Nml::XML_NAMESPACE . '"/>',
            Nml::c()->append('r')->setXmlns()
        );
    }

    public function testSetXmlns6()
    {
        $expected = '<svg'
            . ' xmlns="http://www.w3.org/2000/svg"'
            . ' xmlns:xlink="http://www.w3.org/1999/xlink"/>';
        $actual = TSvg::c('svg')->setXmlns()->getMarkup();
        $this->assertEquals($expected, $actual);
    }

    public function testCreateRoot1()
    {
        $this->assertEquals('<txml>', Hml::createRoot('txml'));
    }

    public function testCreateRoot2()
    {
        $this->assertEquals(XML_DECL . "\n<e/>", Tml::createRoot('e'));
    }

    public function testAddProcessingInstruction1()
    {
        $expected = XML_DECL
            . "\n<?target1 content ?>\n<?target2 attrib=\"value\" ?>\n"
            . Dml::DOCTYPE . "\n<txml xmlns=\"" . Nml::XML_NAMESPACE . "\"/>";
        $xml = Dnml::createRoot('txml');
        $xml->addProcessingInstruction('target1', 'content');
        $xml->addProcessingInstruction('target2')->attrib('attrib', 'value');
        $actual = $xml;
        $this->assertEquals($expected, $actual);
    }

    public function testAddProcessingInstruction2()
    {
        $this->assertEquals(
            '<?target ?>',
            Tml::c()->addProcessingInstruction('target', '')->getMarkup()
        );
    }

    public function testSetCharacterEncodingLineBreakIndentation1()
    {
        $expected = '<?xml version="1.0" encoding="ISO-8859-15" ?><r><e/></r>';
        Xml::setCharacterEncoding('ISO-8859-15');
        Xml::setLineBreak('');
        Xml::setIndentation('    ');
        $xml = Xml::createRoot('r')->append('e')->getRoot()->getMarkup();
        Xml::setCharacterEncoding('UTF-8');
        Xml::setLineBreak("\n");
        Xml::setIndentation("\t");
        $actual = $xml;
        $this->assertEquals($expected, $actual);
    }

    public function testSetCharacterEncodingLineBreakIndentation2()
    {
        $expected = "<?xml version=\"1.0\" encoding=\"ISO-8859-15\" ?>\r"
            . Dml::DOCTYPE
            . "\r<r>\r    <e/>\r</r>";
        Dml::setCharacterEncoding('ISO-8859-15');
        Dml::setLineBreak("\r");
        Dml::setIndentation('    ');
        $xml = Dml::createRoot('r')->append('e')->getRoot()->getMarkup();
        Xml::setCharacterEncoding('UTF-8');
        Xml::setLineBreak("\n");
        Xml::setIndentation("\t");
        $actual = $xml;
        $this->assertEquals($expected, $actual);
    }

    public function testDisableTextIndentation1()
    {
        $this->assertEquals(
            "<e>\n\tlorem\n\tipsum\n\tdolor\n\tsit\n</e>\n"
                . "<f>\n\tlorem\nipsum\ndolor\nsit\n</f>",
            Tml::c()
                ->append('e')
                ->appendText(MULTILINE)
                ->getRoot()
                ->append('f')
                ->disableTextIndentation()
                ->appendText(MULTILINE)
        );
    }

    public function testDisableTextIndentation2()
    {
        $this->assertEquals(
            "<e>\n\t<f>lorem\n\tipsum\n\tdolor\n\tsit</f>"
                . "\n\t<g>lorem\nipsum\ndolor\nsit</g>\n</e>",
            Tml::c('e')
                ->append('f', MULTILINE)
                ->getParent()
                ->append('g', MULTILINE)
                ->disableTextIndentation()
        );
    }

    public function testDisableLineBreak1()
    {
        $this->assertEquals(
            "<e>\n\t<f><g><h/></g></f>\n</e>",
            Tml::c()
                ->append('e')
                ->append('f')
                ->disableLineBreak()
                ->append('g')
                ->append('h')
        );
    }

    public function testDisableLineBreak2()
    {
        $this->assertEquals(
            "<e>\n\t<f><g><![CDATA[lorem ipsum\ndolor sit]]></g></f>\n</e>",
            Tml::c()
                ->append('e')
                ->append('f')
                ->disableLineBreak()
                ->append('g')
                ->cdata()
                ->appendText("lorem ipsum\ndolor sit")
        );
    }

//     public function testVerticalAttributes()
//     {
//         $expected = "<e\n\t\ta1=\"a1\"\n\t\ta2=\"a2\"\n\t\ta3=\"a3\"/>";
//         $actual = Vml::c('e')->attrib('a1')->attrib('a2')->attrib('a3');
//         $this->assertEquals($expected, $actual);
//     }

    /**
     * @dataProvider attribProvider
     */
    public function testAttrib(string $expected, $value, bool $htmlMode = false)
    {
        if ($htmlMode) {
            $actual = Hml::c('e')->attrib('a', $value);
        } else {
            $actual = Tml::c('e')->attrib('a', $value);
        }
        $this->assertEquals($expected, $actual);
    }

    public function attribProvider(): array
    {
        return [
            ['<e a="foo"/>', 'foo'],
            ['<e a=""/>', ''],
            ['<e/>', null],
            ['<e a="a"/>', true],
            ['<e/>', false],
            ['<e a>', true, true],
            ['<e>', false, true],
            ['<e a="all"/>', Media::ALL()],
            ['<e a="1920"/>', 1920],
            ['<e a="1.23456"/>', 1.23456]
        ];
    }

    public function testBoolAttrib1()
    {
        $this->assertEquals(
            "<e e=\"foo\">"
                . "\n\t<f f=\"bar\"/>"
                . "\n\t<f f=\"baz\" bool=\"bool\"/>"
                . "\n\t<f f=\"foo\"/>"
                . "\n</e>",
            Tml::c('e')
                ->attrib('e', 'foo')
                ->append('f')
                ->attrib('f', 'bar')
                ->getParent()
                ->append('f')
                ->attrib('f', 'baz')
                ->getParent()
                ->append('f')
                ->attrib('f', 'foo')
                ->getParent()
                ->boolAttrib('bool', 'baz', 'f')
        );
    }

    public function testBoolAttrib2()
    {
        $this->assertEquals(
            "<e e=\"foo\">"
                . "\n\t<f f=\"bar\" bool=\"bool\"/>"
                . "\n\t<f f=\"baz\"/>"
                . "\n\t<f f=\"foo\" bool=\"bool\"/>"
                . "\n</e>",
            Tml::c('e')
                ->attrib('e', 'foo')
                ->append('f')
                ->attrib('f', 'bar')
                ->getParent()
                ->append('f')
                ->attrib('f', 'baz')
                ->getParent()
                ->append('f')
                ->attrib('f', 'foo')
                ->getParent()
                ->boolAttrib('bool', [
                    'zab',
                    'foo',
                    'bar',
                    null
                ], 'f')
        );
    }

    public function testBoolAttrib3()
    {
        $this->assertEquals(
            "<e e=\"foo\" bool=\"bool\">"
                . "\n\t<f f=\"bar\"/>"
                . "\n\t<f f=\"baz\"/>"
                . "\n\t<f f=\"foo\"/>"
                . "\n</e>",
            Tml::c('e')
                ->attrib('e', 'foo')
                ->append('f')
                ->attrib('f', 'bar')
                ->getParent()
                ->append('f')
                ->attrib('f', 'baz')
                ->getParent()
                ->append('f')
                ->attrib('f', 'foo')
                ->getParent()
                ->boolAttrib('bool', true)
        );
    }

    public function testBoolAttrib4()
    {
        $this->assertEquals(
            '<e/>',
            Tml::c('e')->boolAttrib('a', 'v', null)->getMarkup()
        );
    }

    public function testBoolAttrib5()
    {
        $this->assertEquals(
            '<e/>',
            Tml::c('e')
                ->attrib('a', null)
                ->boolAttrib('b', null, 'a')
                ->getMarkup()
        );
    }

    public function testGetContentDispositionHeaderfield1()
    {
        $this->assertEquals(
            'Content-Disposition: attachment; filename="test.xml"',
            Xml::getContentDispositionHeaderfield('test')
        );
    }

    public function testGetContentDispositionHeaderfield2()
    {
        $this->assertEquals(
            'Content-Disposition: attachment; filename="test.kml"',
            Fml::getContentDispositionHeaderfield('test.kml')
        );
    }

    public function testGetContentTypeHeaderfield()
    {
        $this->assertEquals(
            'Content-Type: application/xml; charset=UTF-8',
            Xml::getContentTypeHeaderfield()
        );
    }
}
