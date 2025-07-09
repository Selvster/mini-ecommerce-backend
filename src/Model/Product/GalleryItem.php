<?php

namespace App\Model\Product;

use App\Model\AbstractModel;

class GalleryItem extends AbstractModel
{
    private string $imageUrl;

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }
}