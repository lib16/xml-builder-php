<?php

namespace Lib16\XML\Tests\Shared;

require_once 'vendor/autoload.php';

use Lib16\Utils\Enums\CSS\Media;
use Lib16\Utils\Enums\Mime\StyleType;
use Lib16\XML\Shared\XmlStylesheet;
use Lib16\XML\Tests\XmlTestCase;
use Lib16\XML\Tests\Tml;

class StyledXml extends Tml
{
	use XmlStylesheet;
}

class XmlStylesheetTest extends XmlTestCase
{
	public function provider()
	{
		return [
			[
				function() {
					$xml = StyledXml::createRoot('styled');
					$xml->xmlStylesheet('style.css');
					return $xml;
				},
				self::XML_DECL . "\n" .
				'<?xml-stylesheet href="style.css" ?>' . "\n" .
				'<styled/>'
			],
			[
				function() {
					$xml = StyledXml::createRoot('styled');
					$xml->xmlStylesheet('one.css', true, 'One',
							StyleType::CSS(), 'UTF-8', Media::SCREEN(), Media::PRINT());
					$xml->xmlStylesheet('two.css')
							->setMedia(Media::SCREEN(), Media::PRINT())
							->setType(StyleType::CSS())
							->setAlternate()
							->setTitle('Two')
							->setCharset('UTF-8');
					return $xml;
				},
				self::XML_DECL . "\n" .
				'<?xml-stylesheet href="one.css" type="text/css"' .
				' media="screen,print" alternate="yes" title="One" charset="UTF-8" ?>' . "\n" .
				'<?xml-stylesheet href="two.css" type="text/css"' .
				' media="screen,print" alternate="yes" title="Two" charset="UTF-8" ?>' . "\n" .
				'<styled/>'
			]
		];
	}
}