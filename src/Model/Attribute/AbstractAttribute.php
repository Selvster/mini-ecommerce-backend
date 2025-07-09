<?php

namespace App\Model\Attribute;

use App\Model\AbstractModel;

abstract class AbstractAttribute extends AbstractModel
{
    protected string $name;
    protected string $type; 
    /** @var AttributeItem[] */
    protected array $items = []; 

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return AttributeItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param AttributeItem[] $items
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    public function addItem(AttributeItem $item): self
    {
        $this->items[] = $item;
        return $this;
    }
}