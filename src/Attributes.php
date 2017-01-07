<?php

namespace Lib16\XML;

use MyCLabs\Enum\Enum;
use Lib16\Utils\Enums\CSS\Unit;
use Lib16\Utils\NumberFormatter;

/**
 * Manages and displays the attributes of an XML element.
 */
class Attributes
{
	private $attributes;

	public function __construct()
	{
		$this->attributes = [];
	}

	/**
	 * Sets an attribute.
	 *
	 * @param  string                      $name
	 * @param  string|bool|Enum|int|float  $value
	 */
	public function set(string $name, $value = true): self
	{
		if (is_string($value) || is_null($value)) {
			$this->attributes[$name] = $value;
		}
		else if (is_bool($value)) {
			$this->attributes[$name] = $value ? $name : null;
		}
		else if ($value instanceof Enum) {
			$this->attributes[$name] = $value->__toString();
		}
		else if (is_int($value) || is_float($value)) {
			$this->attributes[$name] = '' . $value;
		}
		return $this;
	}

	/**
	 * Sets a boolean attribute by comparing one or more values
	 * with the value of another attribute.
	 *
	 * Helpful for attributes like <code>selected</code> or <code>checked</code> in HTML.
	 *
	 * @param  string  $name
	 * @param  string  $comparisonAttribute  Name of the attribute to compare with.
	 * @param  string  ...$values            Values to compare.
	 */
	public function setByComparison(string $name,
			string $comparisonAttribute, ...$values): self
	{
		$compareTo = $this->attributes[$comparisonAttribute];
		if (is_null($compareTo)) {
			return $this->set($name, false);
		}
		foreach ($values as $value) {
			if (is_null($value)) {
				continue;
			}
			if ($value == $compareTo) {
				return $this->set($name, true);
			}
		}
		return $this->set($name, false);
	}

	/**
	 * Sets or appends to a composable attribute like
	 * <code>class</code> (HTML) or <code>points</code> (SVG).
	 *
	 * @param  string       $name
	 * @param  string       $delimiter  The boundary string.
	 * @param  bool         $check      Whether multiple entries shall be accepted or not.
	 * @param  string|null  ...$parts   Strings to append to the current attribute value.
	 */
	public function setComplex(string $name, string $delimiter, bool $check, ...$parts): self
	{
		$value = $this->attributes[$name] ?? null;
		if (!$check) {
			$value = array_merge([$value], $parts);
		}
		else {
			$value = array_unique(array_merge(explode($delimiter, $value), $parts));
		}
		$value = implode($delimiter, array_filter($value));
		return $this->set($name, $value == '' ? null : $value);
	}

	/**
	 * Sets a number attribute.
	 */
	public function setNumber(string $name,
			$value, NumberFormatter $formatter, Unit $unit = null): self
	{
		return $this->set($name, $formatter->format($value, $unit));
	}

	/**
	 * Sets a number attribute which accepts multiple values.
	 *
	 * @param  string           $name
	 * @param  string           $delimiter   The boundary string.
	 * @param  NumberFormatter  $formatter
	 * @param  Unit             $unit
	 * @param  int|float|null   ...$numbers
	 */
	public function setNumbers(string $name,
			string $delimiter, NumberFormatter $formatter, Unit $unit = null, ...$numbers)
	{
		foreach ($numbers as $i => $number) {
			$numbers[$i] = $formatter->format($number, $unit);;
		}
		return $this->setComplex($name, $delimiter, false, ...$numbers);
	}

	/**
	 * @param string     $name
	 * @param string     $delimiter
	 * @param Enum|null  ...$values
	 */
	public function setEnums(string $name, string $delimiter, ...$values): self
	{
		foreach ($values as $i => $value) {
			$values[$i] = is_null($value) ? null : $value->__toString();
		}
		return $this->setComplex($name, $delimiter, true, ...$values);
	}

	public function setNull(string ...$names): self
	{
		foreach ($names as $name) {
			$this->set($name, null);
		}
		return $this;
	}

	/**
	 * Replaces previous attribute list.
	 */
	public function setAttributes(Attributes $attributes): self
	{
		$this->attributes = $attributes->attributes;
		return $this;
	}

	/**
	 * Called by the <code>Xml</code> method <code>getMarkup()</code>.
	 */
	public function getMarkup(bool $htmlMode = false, $whitespace = ' '): string
	{
		$markup = '';
		foreach ($this->attributes as $name => $value) {
			$markup .= $this->buildAttribStr($name, $value, $htmlMode, $whitespace);
		}
		return $markup;
	}

	public function __toString(): string
	{
		return $this->getMarkup();
	}

	private function buildAttribStr(string $name, $value,
			bool $htmlMode, string $whitespace): string
	{
		if (is_null($value)) {
			return '';
		}
		if ($value == $name && $htmlMode) {
			return $whitespace . $name;
		}
		return $whitespace . $name . '="' . $value . '"';
	}
}