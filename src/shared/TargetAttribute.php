<?php
namespace Lib16\XML\Shared;

trait TargetAttribute
{
    /**
     * @param Target|string $target
     */
    public function setTarget($target = null): self
    {
        return $this->attrib("target", $target);
    }
}
