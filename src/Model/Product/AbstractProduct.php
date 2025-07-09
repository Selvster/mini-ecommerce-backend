<?php

namespace App\Model\Product;

use App\Model\AbstractModel;
use App\Model\Category;
use App\Model\Price;
use App\Model\Attribute\AbstractAttribute; 
abstract class AbstractProduct extends AbstractModel
{
    protected string $productId; 
    protected string $name;
    protected bool $inStock;
    protected ?string $description = null;
    protected string $brand;
    protected ?Category $category = null;

    /** @var GalleryItem[] */
    protected array $gallery = [];

    /** @var Price[] */
    protected array $prices = [];

    /** @var AbstractAttribute[] */ 
    protected array $attributeSets = [];

    // --- Getters ---
    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getName(): string
    {

        return $this->name;
    }

    public function isInStock(): bool
    {
        return $this->inStock;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @return GalleryItem[]
     */
    public function getGallery(): array
    {
        return $this->gallery;
    }

    /**
     * @return Price[]
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @return AbstractAttribute[]
     */
    public function getAttributeSets(): array
    {
        return $this->attributeSets;
    }

    // --- Setters ---
    public function setProductId(string $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setInStock(bool $inStock): self
    {
        $this->inStock = $inStock;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @param GalleryItem[] $gallery
     */
    public function setGallery(array $gallery): self
    {
        $this->gallery = $gallery;
        return $this;
    }

    public function addGalleryItem(GalleryItem $item): self
    {
        $this->gallery[] = $item;
        return $this;
    }

    /**
     * @param Price[] $prices
     */
    public function setPrices(array $prices): self
    {
        $this->prices = $prices;
        return $this;
    }

    public function addPrice(Price $price): self
    {
        $this->prices[] = $price;
        return $this;
    }

    /**
     * @param AbstractAttribute[] $attributeSets
     */
    public function setAttributeSets(array $attributeSets): self
    {
        $this->attributeSets = $attributeSets;
        return $this;
    }

    public function addAttributeSet(AbstractAttribute $attributeSet): self
    {
        $this->attributeSets[] = $attributeSet;
        return $this;
    }

}