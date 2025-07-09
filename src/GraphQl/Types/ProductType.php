<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Model\Product\Product;
use App\Model\Category;
use App\Model\Product\GalleryItem;
use App\Model\Price;
use App\Model\Attribute\AbstractAttribute;
use App\GraphQL\TypeRegistry;

class ProductType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Product',
            'description' => 'A product in the store',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'The unique identifier of the product',
                    'resolve' => function (Product $product): string {
                        return $product->getProductId();
                    }
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The name of the product',
                    'resolve' => function (Product $product): string {
                        return $product->getName();
                    }
                ],
                'inStock' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the product is currently in stock',
                    'resolve' => function (Product $product): bool {
                        return $product->isInStock();
                    }
                ],
                'gallery' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::galleryItem()))),
                    'description' => 'List of image URLs for the product',
                    'resolve' => function (Product $product): array {
                        return $product->getGallery();
                    }
                ],
                'description' => [
                    'type' => Type::string(),
                    'description' => 'A detailed description of the product',
                    'resolve' => function (Product $product): ?string {
                        return $product->getDescription();
                    }
                ],
                'category' => [
                    'type' => Type::nonNull(TypeRegistry::category()),
                    'description' => 'The category the product belongs to',
                    'resolve' => function (Product $product): Category {
                        return $product->getCategory();
                    }
                ],
                'attributes' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::attributeSet()))),
                    'description' => 'List of attribute sets for the product',
                    'resolve' => function (Product $product): array {
                        return $product->getAttributeSets();
                    }
                ],
                'prices' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::price()))),
                    'description' => 'List of prices for the product in different currencies',
                    'resolve' => function (Product $product): array {
                        return $product->getPrices();
                    }
                ],
                'brand' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The brand of the product',
                    'resolve' => function (Product $product): string {
                        return $product->getBrand();
                    }
                ],
            ],
        ]);
    }
}