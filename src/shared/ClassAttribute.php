<?php

namespace Lib16\XML\Shared;

trait ClassAttribute
{
	public function setClass(string ...$classes): self
	{
		$this->attributes->setComplex('class', ' ', true, ...$classes);
		return $this;
	}

	/**
	 * Adds alternating classes to child elements.
	 *
	 * @param  int          $column
	 * @param  string|null  ...$classes
	 */
	public function stripes(int $column = -1, ...$classes): self
	{
		$n = count($classes);
		$index = -1;
		$prev = null;
		for ($i = 0; $i < $this->countChildElements(); $i++) {
			$row = $this->getChild($i);
			if ($column < 0) {
				$index = ++$index % $n;
			}
			else {
				for ($k = 0; $k < $row->countChildElements(); $k++) {
					$cell = $row->getChild($k);
					if ($k > $column) {
						break;
					}
					if ($i == 0 || $cell->getContent() != $prev->getChild($k)->getContent()) {
						$index = ++$index % $n;
						break;
					}
				}
				$prev = $row;
			}
			if (!is_null($classes[$index]))
				$this->getChild($i)->setClass($classes[$index]);
		}
		return $this;
	}
}
