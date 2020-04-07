<?php
namespace Lib16\XML\Tests;

require_once 'vendor/autoload.php';

use Lib16\Utils\NumberFormatter;
use Lib16\Utils\Enums\CSS\ {
    LengthUnit,
    Media
};
use Lib16\XML\Attributes;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{

    public function provider(): array
    {
        $f = new NumberFormatter(4);
        return [
            // set()
            [
                (new Attributes())->set('a', 'foo'),
                ' a="foo"'
            ],
            [
                (new Attributes())->set('a', ''),
                ' a=""'
            ],
            [
                (new Attributes())->set('a', null),
                ''
            ],
            [
                (new Attributes())->set('a'),
                ' a="a"'
            ],
            [
                (new Attributes())->set('a', true),
                ' a="a"'
            ],
            [
                (new Attributes())->set('a', false),
                ''
            ],
            [
                (new Attributes())->set('a', true)->getMarkup(true),
                ' a'
            ],
            [
                (new Attributes())->set('a', false)->getMarkup(true),
                ''
            ],
            [
                (new Attributes())->set('a', Media::ALL()),
                ' a="all"'
            ],
            [
                (new Attributes())->set('a', 1920),
                ' a="1920"'
            ],
            [
                (new Attributes())->set('a', 1.23456),
                ' a="1.23456"'
            ],

            // setByComparison()
            [
                (new Attributes())->set('a1', 'foo')->setByComparison('a2', 'a1', 'foo'),
                ' a1="foo" a2="a2"'
            ],
            [
                (new Attributes())->set('a1', 'bar')->setByComparison('a2', 'a1', 'foo'),
                ' a1="bar"'
            ],
            [
                (new Attributes())->set('a1', null)->setByComparison('a2', 'a1', 'foo'),
                ''
            ],
            [
                (new Attributes())->set('a1', 'foo')->setByComparison('a2', 'a1', 'bar', 'baz'),
                ' a1="foo"'
            ],
            [
                (new Attributes())->set('a1', 'foo')->setByComparison('a2', 'a1'),
                ' a1="foo"'
            ],

            // setComplex()
            [
                (new Attributes())->set('a', 'foo,bar')->setComplex('a', ',', false, 'baz', 'foo', 'baz'),
                ' a="foo,bar,baz,foo,baz"'
            ],
            [
                (new Attributes())->set('a', 'foo,bar')->setComplex('a', ',', true, 'baz', 'foo', 'baz'),
                ' a="foo,bar,baz"'
            ],
            [
                (new Attributes())->set('a', 'foo')->setComplex('a', ',', true, 'foo'),
                ' a="foo"'
            ],
            [
                (new Attributes())->set('a', 'foo,baz')->setComplex('a', ',', true, 'foo'),
                ' a="foo,baz"'
            ],
            [
                (new Attributes())->set('a', 'baz,foo')->setComplex('a', ',', true, 'foo'),
                ' a="baz,foo"'
            ],
            [
                (new Attributes())->set('a', 'baz,foo,baz')->setComplex('a', ',', true, 'foo'),
                ' a="baz,foo"'
            ],
            [
                (new Attributes())->set('a', null)->setComplex('a', ',', true, 'foo'),
                ' a="foo"'
            ],
            [
                (new Attributes())->setComplex('a', ',', true, 'foo', null, 'baz'),
                ' a="foo,baz"'
            ],

            // setNumber()
            [
                (new Attributes())->setNumber('a', 12.3456789, $f),
                ' a="12.3457"'
            ],
            [
                (new Attributes())->setNumber('a', 12.3, $f),
                ' a="12.3"'
            ],
            [
                (new Attributes())->setNumber('a', 12, $f),
                ' a="12"'
            ],
            [
                (new Attributes())->setNumber('a', 16, $f, LengthUnit::PX()),
                ' a="16px"'
            ],
            [
                (new Attributes())->setNumber('a', 50, $f, LengthUnit::PERCENT()),
                ' a="50%"'
            ],
            [
                (new Attributes())->setNumber('a', 1.5, $f, LengthUnit::NONE()),
                ' a="1.5"'
            ],
            [
                (new Attributes())->setNumber('a', 1.5, $f, null),
                ' a="1.5"'
            ],
            [
                (new Attributes())->setNumber('a', null, $f),
                ''
            ],

            // setNumbers()
            [
                (new Attributes())->setNumbers('a', ' ', $f, null, 10.5, 5.25, 0)
                    ->setNumbers('a', ' ', $f, null, 10.5)
                    ->setNumbers('a', ' ', $f),
                ' a="10.5 5.25 0 10.5"'
            ],
            [
                (new Attributes())->setNumbers('a', ' ', $f),
                ''
            ],
            [
                (new Attributes())->setNumbers('a', ' ', $f, LengthUnit::PX()),
                ''
            ],
            [
                (new Attributes())->setNumbers('a', ' ', $f, LengthUnit::PX(), 10.00001, 5)
                    ->setNumbers('a', ' ', $f, LengthUnit::PX(), 10)
                    ->setNumbers('a', ' ', $f),
                ' a="10px 5px 10px"'
            ],

            // setEnums()
            [
                (new Attributes())->setEnums('a', ' ', Media::SCREEN(), Media::PRINT()),
                ' a="screen print"'
            ],
            [
                (new Attributes())->setEnums('a', ' ', Media::SCREEN(), null, Media::PRINT()),
                ' a="screen print"'
            ],

            // vertical alignment
            [
                (new Attributes())->set('a1', 'value 1')
                    ->set('a2', 'value 2')
                    ->getMarkup(false, "\n\t\t"),
                "\n\t\ta1=\"value 1\"\n\t\ta2=\"value 2\""
            ]
        ];
    }

    /**
     *
     * @dataProvider provider
     */
    public function test($actual, $expected)
    {
        if ($actual instanceof Attributes) {
            $actual = $actual->getMarkup();
        }
        $this->assertEquals($expected, $actual);
    }
}
