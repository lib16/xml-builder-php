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
			[Aml::c()->section()->setClass('main'), '<section class="main">'],
			[Aml::c()->section()->setClass(null), '<section>'],
			[Aml::c()->option()->setSelected(), '<option selected>'],
			[Aml::c()->option()->setSelected(true), '<option selected>'],
			[Aml::c()->option()->setSelected(false), '<option>'],
			[Aml::em('lorem') . " ipsum" . Aml::br(), '<em>lorem</em> ipsum<br>'],
			[
				Aml::article()->setClass('overview')->header()->h1('PHP'),
				"<article class=\"overview\">\n\t<header>\n\t\t<h1>PHP</h1>\n\t</header>\n</article>"
			]
		];
	}
}
