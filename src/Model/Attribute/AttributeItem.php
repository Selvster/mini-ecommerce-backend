<?php

namespace App\Model\Attribute;

use App\Model\AbstractModel;

class AttributeItem extends AbstractModel
{
    private string $displayValue; 
    private string $value;       

    public function getDisplayValue(): string
    {
        return $this->displayValue;
    }

    public function setDisplayValue(string $displayValue): self
    {
        $this->displayValue = $displayValue;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }
}