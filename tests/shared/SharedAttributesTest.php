<?php
namespace Lib16\XML\Tests\Shared;

use Lib16\Utils\Enums\CSS\Media;
use Lib16\XML\Shared\ {
    ClassAttribute,
    MediaAttribute,
    Target,
    TargetAttribute,
    TitleAttribute,
    Space,
    XmlAttributes
};
use Lib16\XML\Tests\Tml;
use PHPUnit\Framework\TestCase;

class MyXml extends Tml
{
    use ClassAttribute, MediaAttribute, TargetAttribute, TitleAttribute, XmlAttributes;
}

class SharedAttributesTest extends TestCase
{
    public function testSetClass()
    {
        $this->assertEquals(
            '<e class="lorem ipsum dolores dolor"/>',
            MyXml::c('e')
                ->setClass('lorem', 'ipsum', 'dolores')
                ->setClass('dolor', 'ipsum', 'lorem')
        );
    }

    public function testStripes1()
    {
        $expected = '<table>'
            . "\n\t<tr class=\"a-1st\">"
            . "\n\t\t<td>Foo</td>"
            . "\n\t\t<td>Berlin</td>"
            . "\n\t\t<td>20</td>"
            . "\n\t</tr>"
            . "\n\t<tr class=\"a-1st c-2nd\">"
            . "\n\t\t<td>Foo</td>"
            . "\n\t\t<td>Berlin</td>"
            . "\n\t\t<td>12</td>"
            . "\n\t</tr>"
            . "\n\t<tr class=\"a-1st b-2nd\">"
            . "\n\t\t<td>Foo</td>"
            . "\n\t\t<td>Cologne</td>"
            . "\n\t\t<td>12</td>"
            . "\n\t</tr>"
            . "\n\t<tr class=\"a-2nd b-3rd c-2nd\">"
            . "\n\t\t<td>Bar</td>"
            . "\n\t\t<td>Cologne</td>"
            . "\n\t\t<td>12</td>"
            . "\n\t</tr>"
            . "\n\t<tr class=\"a-2nd\">"
            . "\n\t\t<td>Bar</td>"
            . "\n\t\t<td>Hamburg</td>"
            . "\n\t\t<td>15</td>"
            . "\n\t</tr>"
            . "\n\t<tr class=\"a-2nd c-2nd\">"
            . "\n\t\t<td>Bar</td>"
            . "\n\t\t<td>Hamburg</td>"
            . "\n\t\t<td>15</td>"
            . "\n\t</tr>"
            . "\n</table>";
        $actual = self::table()
            ->stripes(0, 'a-1st', 'a-2nd')
            ->stripes(1, null, 'b-2nd', 'b-3rd')
            ->stripes(- 1, null, 'c-2nd');
        $this->assertEquals($expected, $actual);
    }

    public function testStripes2()
    {
        $this->assertEquals(
            '<ul>'
                . "\n\t<li>Berlin</li>"
                . "\n\t<li class=\"a-2nd\">Hamburg</li>"
                . "\n\t<li>Munich</li>\n</ul>",
            self::list()->stripes(- 1, null, 'a-2nd')
        );
    }

    public function testSetMedia()
    {
        $this->assertEquals(
            '<e media="screen,print"/>',
            MyXml::c('e')->setMedia(Media::SCREEN(), Media::PRINT())->setMedia()
        );
    }

    /**
     * @dataProvider setTargetProvider
     */
    public function testSetTarget(string $expected, string $target)
    {
        $this->assertEquals($expected, MyXml::c('e')->setTarget($target));
    }

    public function setTargetProvider(): array
    {
        return [
            ['<e target="index.html"/>', 'index.html'],
            ['<e target="_blank"/>', Target::BLANK()],
            ['<e target="_parent"/>', Target::PARENT()],
            ['<e target="_self"/>', Target::SELF()],
            ['<e target="_top"/>', Target::TOP()]
        ];
    }

    public function testSetTitle()
    {
        $this->assertEquals(
            '<e title="Lorem Ipsum"/>',
            MyXml::c('e')->setTitle('Lorem Ipsum')
        );
    }

    public function testSetLang()
    {
        $this->assertEquals(
            "<e xml:lang=\"fr\"/>",
            MyXml::c('e')->setLang('fr')
        );
    }

    public function testSetSpace1()
    {
        $this->assertEquals(
            "<e xml:space=\"default\"/>",
            MyXml::c('e')->setSpace(Space::DEFAULT())
        );
    }

    public function testSetSpace2()
    {
        $this->assertEquals(
            "<e xml:space=\"preserve\"/>",
            MyXml::c('e')->setSpace(Space::PRESERVE())
        );
    }

    public function testSetBase()
    {
        $this->assertEquals(
            "<e xml:base=\"http://example.com\"/>",
            MyXml::c('e')->setBase('http://example.com')
        );
    }

    public function testSetId()
    {
        $this->assertEquals(
            "<e xml:id=\"foo\"/>",
            MyXml::c('e')->setId('foo')
        );
    }

    public static function table()
    {
        $table = MyXml::c('table');
        $tr = $table->append('tr');
        $tr->append('td', 'Foo');
        $tr->append('td', 'Berlin');
        $tr->append('td', '20');
        $tr = $table->append('tr');
        $tr->append('td', 'Foo');
        $tr->append('td', 'Berlin');
        $tr->append('td', '12');
        $tr = $table->append('tr');
        $tr->append('td', 'Foo');
        $tr->append('td', 'Cologne');
        $tr->append('td', '12');
        $tr = $table->append('tr');
        $tr->append('td', 'Bar');
        $tr->append('td', 'Cologne');
        $tr->append('td', '12');
        $tr = $table->append('tr');
        $tr->append('td', 'Bar');
        $tr->append('td', 'Hamburg');
        $tr->append('td', '15');
        $tr = $table->append('tr');
        $tr->append('td', 'Bar');
        $tr->append('td', 'Hamburg');
        $tr->append('td', '15');
        return $table;
    }

    public static function list()
    {
        $list = MyXml::c('ul');
        $list->append('li', 'Berlin');
        $list->append('li', 'Hamburg');
        $list->append('li', 'Munich');
        return $list;
    }
}
