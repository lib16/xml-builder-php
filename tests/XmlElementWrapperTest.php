<?php
namespace Lib16\XML\Tests;

use PHPUnit\Framework\TestCase;
use Lib16\XML\Xml;
use Lib16\XML\XmlElementWrapper;

class XmlElementWrapperTest extends TestCase
{
    public function test()
    {
        $dt = new \DateTime('2020-02-02 20:20:00');
        $dt->setTimezone(new \DateTimeZone('Europe/Berlin'));

        $html = Html::create();
        $head = $html->head()->title('The Title');
        $body = $html->body()->attrib('class', 'pg-static');

        $this->assertEquals(
            "<html>\n"
            . "\t<head>\n"
            . "\t\t<title>The Title</title>\n"
            . "\t</head>\n"
            . "\t<body class=\"pg-static\"></body>\n"
            . "</html>",
            $body->__toString()
        );

        $this->assertEquals(
            "<body class=\"pg-static\"></body>",
            $body->getMarkup()
        );

        $body->appendDateTime('p', $dt);

        $this->assertEquals(
            "<body class=\"pg-static\">\n"
            . "\t<p>Sun, 02 Feb 2020 20:20:00 +0100</p>\n"
            . "</body>",
            $body->getMarkup()
        );
    }
}

class HtmlMarkup extends Xml
{
    const HTML_MODE_ENABLED = true;

    const XML_DECLARATION_ENABLED = false;
}

class HtmlElementWrapper extends XmlElementWrapper
{
    const END_TAG_OMISSION = false;
}

class Html extends HtmlElementWrapper
{
    const NAME = 'html';

    public static function create(): self
    {
        return new self(HtmlMarkup::createRoot(self::NAME));
    }

    public function head(): Head
    {
        return Head::appendTo($this);
    }

    public function body(): Body
    {
        return Body::appendTo($this);
    }
}

class Head extends HtmlElementWrapper
{
    const NAME = 'head';

    public function title(string $title): self
    {
        return self::append('title', $title);
    }
}

class Body extends HtmlElementWrapper
{
    const NAME = 'body';
}