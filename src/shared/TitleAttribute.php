<?php

namespace Lib16\XML\Shared;

trait TitleAttribute
{
	public function setTitle(string $title = null): self
	{
		return $this->attrib('title', $title);
	}
}