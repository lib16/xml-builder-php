<?php
namespace Lib16\XML\Shared;

/**
 * <code>xml:lang</code>, <code>xml:space</code>, <code>xml:base</code> and <code>xml:id</code>.
 */
trait XmlAttributes
{

    /**
     *
     * @param string|null $lang
     *            A BCP 47 language tag. For example "en" or "fr-CA".
     */
    public function setLang(string $lang = null): self
    {
        return $this->attrib('xml:lang', $lang);
    }

    public function setSpace(Space $space = null): self
    {
        return $this->attrib('xml:space', $space);
    }

    public function setBase(string $base = null): self
    {
        return $this->attrib('xml:base', $base);
    }

    public function setId(string $id = null): self
    {
        return $this->attrib('xml:id', $id);
    }
}