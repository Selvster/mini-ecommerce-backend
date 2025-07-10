<?php

namespace App\Model\Order;

use App\Model\AbstractModel;
use App\Model\Currency;
use Brick\Math\BigDecimal;

class Order extends AbstractModel
{
    private \DateTimeImmutable $createdAt;
    private BigDecimal $totalAmount;
    private ?Currency $currency = null;

    /** @var OrderItem[] */
    private array $items = [];

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getTotalAmount(): BigDecimal
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(BigDecimal $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param OrderItem[] $items
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    public function addItem(OrderItem $item): self
    {
        $this->items[] = $item;
        return $this;
    }
}