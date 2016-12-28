<?php

namespace Lib16\XML\Shared\XLink;

/**
 * The XLink attributes, but for element type <code>simple</code> only.
 */
trait XLink
{
	public function setXLinkNamespace(): self
	{
		return $this->setXmlns(XLinkConstants::NAMESPACE, XLinkConstants::NAMESPACE_PREFIX);
	}

	public function setXLinkType(Type $type = null): self
	{
		$type = $type ?? Type::SIMPLE;
		return $this->attrib(XLinkConstants::NAMESPACE_PREFIX . ':type', $type);
	}

	public function setXLinkHref(string $href): self
	{
		return $this->attrib(XLinkConstants::NAMESPACE_PREFIX . ':href', $href);
	}

	public function setXLinkRole(string $role): self
	{
		return $this->attrib(XLinkConstants::NAMESPACE_PREFIX . ':role', $role);
	}

	public function setXLinkArcrole(string $arcrole): self
	{
		return $this->attrib(XLinkConstants::NAMESPACE_PREFIX . ':arcrole', $arcrole);
	}

	public function setXLinkTitle(string $title): self
	{
		return $this->attrib(XLinkConstants::NAMESPACE_PREFIX . ':title', $title);
	}

	public function setXLinkShow(Show $show): self
	{
		return $this->attrib(XLinkConstants::NAMESPACE_PREFIX . ':show', $show);
	}

	public function setXLinkActuate(Actuate $actuate): self
	{
		return $this->attrib(XLinkConstants::NAMESPACE_PREFIX . ':actuate', $actuate);
	}
}