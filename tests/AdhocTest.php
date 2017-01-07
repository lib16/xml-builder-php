<?php

namespace Lib16\XML\Tests;

require_once 'vendor/autoload.php';
require_once 'tests/xmlClasses.php';

use Lib16\XML\Xml;
use Lib16\XML\Adhoc;

class Aml extends Hml
{
	use Adhoc;
}

class AdhocTest extends XmlTestCase
{
	public function provider(): array
	{
		return [
			// Adhoc
			[Aml::cs()->section()->setClass('main'), '<section class="main">'],
			[Aml::cs()->section()->setClass(null), '<section>'],
			[Aml::cs()->option()->setSelected(), '<option selected>'],
			[Aml::cs()->option()->setSelected(true), '<option selected>'],
			[Aml::cs()->option()->setSelected(false), '<option>'],
			[Aml::em('lorem') . " ipsum" . Aml::br(), '<em>lorem</em> ipsum<br>']
		];
	}
}
