<?php

namespace App\Model\Attribute;

class SwatchAttribute extends AbstractAttribute
{
    public function __construct()
    {
        $this->type = 'swatch';
    }

    /**
     *
     * @param AttributeItem $item The specific AttributeItem to get the hex color from.
     * @return string The hex color value.
     */
    public function getHexColor(AttributeItem $item): string
    {
        return $item->getValue();
    }
}