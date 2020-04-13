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
    public $f;

    public function setUp()
    {
        $this->f = new NumberFormatter(4);
    }

    public function testSet1()
    {
        $this->assertEquals(
            ' a="foo"',
            (new Attributes())->set('a', 'foo')
        );
    }

    public function testSet2()
    {
        $this->assertEquals(
            ' a=""',
            (new Attributes())->set('a', '')
        );
    }

    public function testSet3()
    {
        $this->assertEquals(
            '',
            (new Attributes())->set('a', null)
        );
    }

    public function testSet4()
    {
        $this->assertEquals(
            ' a="a"',
            (new Attributes())->set('a')
        );
    }

    public function testSet5()
    {
        $this->assertEquals(
            ' a="a"',
            (new Attributes())->set('a', true)
        );
    }

    public function testSet6()
    {
        $this->assertEquals(
            '',
            (new Attributes())->set('a', false)
        );
    }

    public function testSet7()
    {
        $this->assertEquals(
            ' a',
            (new Attributes())->set('a', true)->getMarkup(true)
        );
    }

    public function testSet8()
    {
        $this->assertEquals(
            '',
            (new Attributes())->set('a', false)->getMarkup(true)
        );
    }

    public function testSet9()
    {
        $this->assertEquals(
            ' a="all"',
            (new Attributes())->set('a', Media::ALL())
        );
    }

    public function testSet10()
    {
        $this->assertEquals(
            ' a="1920"',
            (new Attributes())->set('a', 1920)
        );
    }

    public function testSet11()
    {
        $this->assertEquals(
            ' a="1.23456"',
            (new Attributes())->set('a', 1.23456)
        );
    }

    public function testSetByComparison1()
    {
        $this->assertEquals(
            ' a1="foo" a2="a2"',
            (new Attributes())
                ->set('a1', 'foo')
                ->setByComparison('a2', 'a1', 'foo')
        );
    }

    public function testSetByComparison2()
    {
        $this->assertEquals(
            ' a1="bar"',
            (new Attributes())
                ->set('a1', 'bar')
                ->setByComparison('a2', 'a1', 'foo')
        );
    }

    public function testSetByComparison3()
    {
        $this->assertEquals(
            '',
            (new Attributes())
                ->set('a1', null)
                ->setByComparison('a2', 'a1', 'foo')
        );
    }

    public function testSetByComparison4()
    {
        $this->assertEquals(
            ' a1="foo"',
            (new Attributes())
                ->set('a1', 'foo')
                ->setByComparison('a2', 'a1', null, 'bar', 'baz')
        );
    }

    public function testSetByComparison5()
    {
        $this->assertEquals(
            ' a1="foo"',
            (new Attributes())
                ->set('a1', 'foo')
                ->setByComparison('a2', 'a1')
        );
    }

    public function testSetComplex1()
    {
        $this->assertEquals(
            ' a="foo,bar,baz,foo,baz"',
            (new Attributes())
                ->set('a', 'foo,bar')
                ->setComplex('a', ',', false, 'baz', 'foo', 'baz')
        );
    }

    public function testSetComplex2()
    {
        $this->assertEquals(
            ' a="foo,bar,baz"',
            (new Attributes())
                ->set('a', 'foo,bar')
                ->setComplex('a', ',', true, 'baz', 'foo', 'baz')
        );
    }

    public function testSetComplex3()
    {
        $this->assertEquals(
            ' a="foo"',
            (new Attributes())
                ->set('a', 'foo')
                ->setComplex('a', ',', true, 'foo')
        );
    }

    public function testSetComplex4()
    {
        $this->assertEquals(
            ' a="foo,baz"',
            (new Attributes())
                ->set('a', 'foo,baz')
                ->setComplex('a', ',', true, 'foo')
        );
    }

    public function testSetComplex5()
    {
        $this->assertEquals(
            ' a="baz,foo"',
            (new Attributes())
                ->set('a', 'baz,foo')
                ->setComplex('a', ',', true, 'foo')
        );
    }

    public function testSetComplex6()
    {
        $this->assertEquals(
            ' a="baz,foo"',
            (new Attributes())
                ->set('a', 'baz,foo,baz')
                ->setComplex('a', ',', true, 'foo')
        );
    }

    public function testSetComplex7()
    {
        $this->assertEquals(
            ' a="foo"',
            (new Attributes())
                ->set('a', null)
                ->setComplex('a', ',', true, 'foo')
        );
    }

    public function testSetComplex8()
    {
        $this->assertEquals(
            ' a="foo,baz"',
            (new Attributes())->setComplex('a', ',', true, 'foo', null, 'baz')
        );
    }

    public function testSetNumber1()
    {
        $this->assertEquals(
            ' a="12.3457"',
            (new Attributes())->setNumber('a', 12.3456789, $this->f)
        );
    }

    public function testSetNumber2()
    {
        $this->assertEquals(
            ' a="12.3"',
            (new Attributes())->setNumber('a', 12.3, $this->f)
        );
    }

    public function testSetNumber3()
    {
        $this->assertEquals(
            ' a="12"',
            (new Attributes())->setNumber('a', 12, $this->f)
        );
    }

    public function testSetNumber4()
    {
        $this->assertEquals(
            ' a="16px"',
            (new Attributes())->setNumber('a', 16, $this->f, LengthUnit::PX())
        );
    }

    public function testSetNumber5()
    {
        $this->assertEquals(
            ' a="50%"',
            (new Attributes())->setNumber('a', 50, $this->f, LengthUnit::PERCENT())
        );
    }

    public function testSetNumber6()
    {
        $this->assertEquals(
            ' a="1.5"',
            (new Attributes())->setNumber('a', 1.5, $this->f, LengthUnit::NONE())
        );
    }

    public function testSetNumber7()
    {
        $this->assertEquals(
            ' a="1.5"',
            (new Attributes())->setNumber('a', 1.5, $this->f, null)
        );
    }

    public function testSetNumber8()
    {
        $this->assertEquals(
            '',
            (new Attributes())->setNumber('a', null, $this->f)
        );
    }

    public function testSetNumbers1()
    {
        $this->assertEquals(
            ' a="10.5 5.25 0 10.5"',
            (new Attributes())
                ->setNumbers('a', ' ', $this->f, null, 10.5, 5.25, 0)
                ->setNumbers('a', ' ', $this->f, null, 10.5)
                ->setNumbers('a', ' ', $this->f)
        );
    }

    public function testSetNumbers2()
    {
        $this->assertEquals(
            '',
            (new Attributes())->setNumbers('a', ' ', $this->f)
        );
    }

    public function testSetNumbers3()
    {
        $this->assertEquals(
            '',
            (new Attributes())->setNumbers('a', ' ', $this->f, LengthUnit::PX())
        );
    }

    public function testSetNumbers4()
    {
        $this->assertEquals(
            ' a="10px 5px 10px"',
            (new Attributes())
                ->setNumbers('a', ' ', $this->f, LengthUnit::PX(), 10.00001, 5)
                ->setNumbers('a', ' ', $this->f, LengthUnit::PX(), 10)
                ->setNumbers('a', ' ', $this->f)
        );
    }

    public function testSetEnums1()
    {
        $this->assertEquals(
            ' a="screen print"',
            (new Attributes())->setEnums(
                'a',
                ' ',
                Media::SCREEN(),
                Media::PRINT()
            )
        );
    }

    public function testSetEnums2()
    {
        $this->assertEquals(
            ' a="screen print"',
            (new Attributes())->setEnums(
                'a',
                ' ',
                Media::SCREEN(),
                null,
                Media::PRINT()
            )
        );
    }

    public function testSetNull()
    {
        $this->assertEquals(
            '',
            (new Attributes())
                ->set('a1', 'value 1')
                ->set('a2', 'value 2')
                ->setNull('a1', 'a2')
        );
    }

    public function testVerticalAlignment()
    {
        $this->assertEquals(
            "\n\t\ta1=\"value 1\"\n\t\ta2=\"value 2\"",
            (new Attributes())
                ->set('a1', 'value 1')
                ->set('a2', 'value 2')
                ->getMarkup(false, "\n\t\t")
        );
    }














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
