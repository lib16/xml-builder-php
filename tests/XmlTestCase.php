<?php

namespace Lib16\XML\Tests;

use Lib16\XML\Xml;
use PHPUnit\Framework\TestCase;

class XmlTestCase extends TestCase
{
	const XML_DECL = '<?xml version="1.0" encoding="UTF-8" ?>';

	/**
	 * @dataProvider provider
	 */
	public function test($actual, string $expected = null)
	{
		$this->assertExpectedMarkup($actual, $expected);
	}

	public function assertExpectedMarkup($actual, string $expected = null)
	{
		if (is_callable($actual)) {
			$actual = call_user_func($actual);
		}
		if ($actual instanceof Xml) {
			$actual = $actual->__toString();
		}
		$this->assertEquals($expected, $actual);
	}
}
