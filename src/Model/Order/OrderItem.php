<?php

namespace App\Model\Order;

use App\Model\AbstractModel;
use App\Model\Product\Product; 

class OrderItem extends AbstractModel
{
    private int $orderId;
    private string $productId;
    private int $quantity;
    private ?array $selectedAttributes = null; // Stored as JSON, will be decoded to array

    private ?Product $product = null;

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return array|null Decoded selected attributes
     */
    public function getSelectedAttributes(): ?array
    {
        return $this->selectedAttributes;
    }

    /**
     * @param array|null $selectedAttributes Array of ['name' => 'value']
     */
    public function setSelectedAttributes(?array $selectedAttributes): self
    {
        $this->selectedAttributes = $selectedAttributes;
        return $this;
    }
    
     public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

 
}