<?php

namespace Lib16\XML\Shared\Tests;

require_once 'vendor/myclabs/php-enum/src/Enum.php';
require_once 'vendor/lib16/utils/src/enums/Media.php';
require_once 'src/Xml.php';
require_once 'src/Attributes.php';
require_once 'src/shared/MediaAttribute.php';
require_once 'src/shared/TitleAttribute.php';
require_once 'src/shared/XmlStylesheetInstruction.php';
require_once 'src/shared/XmlStylesheet.php';
require_once 'tests/XmlTestCase.php';
require_once 'tests/xmlClasses.php';

use Lib16\Utils\Enums\Media;
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
					$xml->xmlStylesheet('one.css',
							true, 'One', true, 'UTF-8', Media::SCREEN(), Media::PRINT());
					$xml->xmlStylesheet('two.css')
							->setMedia(Media::SCREEN(), Media::PRINT())
							->setType()
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