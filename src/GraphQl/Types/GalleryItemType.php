<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Model\Product\GalleryItem;

class GalleryItemType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'GalleryItem',
            'description' => 'An image URL for a product gallery',
            'fields' => [
                'imageUrl' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The URL of the gallery image',
                    'resolve' => function (GalleryItem $galleryItem): string {
                        return $galleryItem->getImageUrl();
                    },
                ],
            ],
        ]);
    }
}