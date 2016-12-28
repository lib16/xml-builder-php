<?php

namespace Lib16\XML\Tests;

use Lib16\XML\Xml;
use Lib16\XML\Adhoc;

class Tml extends Xml
{
	public static function cs(string $name = null, string $content = null)
	{
		return self::createSub($name, $content);
	}
}

class Hml extends Tml
{
	const HTML_MODE_ENABLED = true;
	const XML_DECLARATION_ENABLED = false;
}

class Fml extends Tml
{
	const FILENAME_EXTENSION = null;
}

class Lml extends Tml
{
	const LINE_BREAK = '';
}

class Nml extends Tml
{
	const XML_NAMESPACE = 'http://example.com/baz';
}

class Dml extends Tml
{
	const DOCTYPE = '<!DOCTYPE txml>';
}

class DNml extends Nml
{
	const DOCTYPE = Dml::DOCTYPE;
}