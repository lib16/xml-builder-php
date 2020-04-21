<?php
namespace Lib16\XML\Tests;

use Lib16\XML\Adhoc;
use PHPUnit\Framework\TestCase;

class Aml extends Hml
{
    use Adhoc;
}

class AdhocTest extends TestCase
{

    public function test1()
    {
        $this->assertEquals(
            '<section class="main">',
            Aml::c()->section()->setClass('main')
        );
    }

    public function test2()
    {
        $this->assertEquals(
            '<section>',
            Aml::c()->section()->setClass(null)
        );
    }

    public function test3()
    {
        $this->assertEquals(
            '<option selected>',
            Aml::c()->option()->setSelected()
        );
    }

    public function test4()
    {
        $this->assertEquals(
            '<option selected>',
            Aml::c()->option()->setSelected(true)
        );
    }

    public function test5()
    {
        $this->assertEquals(
            '<option>',
            Aml::c()->option()->setSelected(false)
        );
    }

    public function test6()
    {
        $this->assertEquals(
            '<em>lorem</em> ipsum<br>',
            Aml::em('lorem') . " ipsum" . Aml::br()
        );
    }

    public function test7()
    {
        $this->assertEquals(
            "<article class=\"overview\">\n\t<header>\n\t\t<h1>PHP</h1>\n\t</header>\n</article>",
            Aml::article()->setClass('overview')->header()->h1('PHP')
        );
    }
}
