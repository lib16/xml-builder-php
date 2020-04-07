<?php
namespace Lib16\XML\Tests\Shared;

require_once 'vendor/autoload.php';

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
use Lib16\XML\Tests\ {
    XmlTestCase,
    Tml
};

class MyXml extends Tml
{
    use ClassAttribute, MediaAttribute, TargetAttribute, TitleAttribute, XmlAttributes;
}

class SharedAttributesTest extends XmlTestCase
{

    public function provider()
    {
        return [
            // ClassAttribute
            [
                MyXml::c('e')->setClass('lorem', 'ipsum', 'dolores')->setClass('dolor', 'ipsum', 'lorem'),
                '<e class="lorem ipsum dolores dolor"/>'
            ],
            [
                self::table()->stripes(0, 'a-1st', 'a-2nd')
                    ->stripes(1, null, 'b-2nd', 'b-3rd')
                    ->stripes(- 1, null, 'c-2nd'),
                '<table>' . "\n\t<tr class=\"a-1st\">" . "\n\t\t<td>Foo</td>" . "\n\t\t<td>Berlin</td>" . "\n\t\t<td>20</td>" . "\n\t</tr>" . "\n\t<tr class=\"a-1st c-2nd\">" . "\n\t\t<td>Foo</td>" . "\n\t\t<td>Berlin</td>" . "\n\t\t<td>12</td>" . "\n\t</tr>" . "\n\t<tr class=\"a-1st b-2nd\">" . "\n\t\t<td>Foo</td>" . "\n\t\t<td>Cologne</td>" . "\n\t\t<td>12</td>" . "\n\t</tr>" . "\n\t<tr class=\"a-2nd b-3rd c-2nd\">" . "\n\t\t<td>Bar</td>" . "\n\t\t<td>Cologne</td>" . "\n\t\t<td>12</td>" . "\n\t</tr>" . "\n\t<tr class=\"a-2nd\">" . "\n\t\t<td>Bar</td>" . "\n\t\t<td>Hamburg</td>" . "\n\t\t<td>15</td>" . "\n\t</tr>" . "\n\t<tr class=\"a-2nd c-2nd\">" . "\n\t\t<td>Bar</td>" . "\n\t\t<td>Hamburg</td>" . "\n\t\t<td>15</td>" . "\n\t</tr>" . "\n</table>"
            ],
            [
                self::list()->stripes(- 1, null, 'a-2nd'),
                '<ul>' . "\n\t<li>Berlin</li>\n\t<li class=\"a-2nd\">Hamburg</li>\n\t<li>Munich</li>\n</ul>"
            ],

            // MediaAttribute
            [
                MyXml::c('e')->setMedia(Media::SCREEN(), Media::PRINT())->setMedia(),
                '<e media="screen,print"/>'
            ],

            // TargetAttribute
            [
                MyXml::c('e')->setTarget('index.html'),
                '<e target="index.html"/>'
            ],
            [
                MyXml::c('e')->setTarget(Target::BLANK()),
                '<e target="_blank"/>'
            ],
            [
                MyXml::c('e')->setTarget(Target::PARENT()),
                '<e target="_parent"/>'
            ],
            [
                MyXml::c('e')->setTarget(Target::SELF()),
                '<e target="_self"/>'
            ],
            [
                MyXml::c('e')->setTarget(Target::TOP()),
                '<e target="_top"/>'
            ],
            [
                MyXml::c('e')->setTitle('Lorem Ipsum'),
                '<e title="Lorem Ipsum"/>'
            ],

            // XmlAttributes
            [
                MyXml::c('e')->setLang('fr'),
                "<e xml:lang=\"fr\"/>"
            ],
            [
                MyXml::c('e')->setSpace(Space::DEFAULT()),
                "<e xml:space=\"default\"/>"
            ],
            [
                MyXml::c('e')->setSpace(Space::PRESERVE()),
                "<e xml:space=\"preserve\"/>"
            ],
            [
                MyXml::c('e')->setBase('http://example.com'),
                "<e xml:base=\"http://example.com\"/>"
            ],
            [
                MyXml::c('e')->setId('foo'),
                "<e xml:id=\"foo\"/>"
            ]
        ];
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
