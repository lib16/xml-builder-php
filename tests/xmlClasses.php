<?php

namespace Lib16\XML\Tests;

use Lib16\XML\Xml;

class Tml extends Xml
{
	public static function c(string $name = null, string $content = null): self
	{
		return static::create($name, $content);
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