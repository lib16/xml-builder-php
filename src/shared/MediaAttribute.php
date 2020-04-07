<?php
namespace Lib16\XML\Shared;

use Lib16\Utils\Enums\CSS\Media;

trait MediaAttribute
{

    public function setMedia(Media ...$media): self
    {
        $this->attributes->setEnums('media', ',', ...$media);
        return $this;
    }
}