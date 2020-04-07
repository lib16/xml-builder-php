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

class XLinkTest extends XmlTestCase
{

    public function provider()
    {
        return [
            [
                XLinkXml1::createRoot('root'),
                self::XML_DECL . "\n<root xmlns:xlink=\"http://www.w3.org/1999/xlink\"/>"
            ],
            [
                XLinkXml2::createRoot('root')->setXLinkNamespace(),
                self::XML_DECL . "\n<root xmlns:xlink=\"http://www.w3.org/1999/xlink\"/>"
            ],
            [
                XLinkXml1::c('e')->setXLinkType(),
                '<e/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkType(Type::SIMPLE()),
                '<e xlink:type="simple"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkHref('image.jpg'),
                '<e xlink:href="image.jpg"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkHref(null),
                '<e/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkShow(Show::EMBED()),
                '<e xlink:show="embed"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkShow(Show::NEW()),
                '<e xlink:show="new"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkShow(Show::REPLACE()),
                '<e xlink:show="replace"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkShow(Show::OTHER()),
                '<e xlink:show="other"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkShow(Show::NONE()),
                '<e xlink:show="none"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkShow(null),
                '<e/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkActuate(Actuate::ONLOAD()),
                '<e xlink:actuate="onLoad"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkActuate(Actuate::ONREQUEST()),
                '<e xlink:actuate="onRequest"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkActuate(Actuate::OTHER()),
                '<e xlink:actuate="other"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkActuate(Actuate::NONE()),
                '<e xlink:actuate="none"/>'
            ],
            [
                XLinkXml1::c('e')->setXLinkActuate(null),
                '<e/>'
            ]
        ];
    }
}
