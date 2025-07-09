<?php

namespace App\GraphQL;

use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\CurrencyType;
use App\GraphQL\Types\PriceType;
use App\GraphQL\Types\ProductType;
use App\GraphQL\Types\GalleryItemType;
use App\GraphQL\Types\AttributeItemType;
use App\GraphQL\Types\AttributeSetType;
use App\GraphQL\Types\OrderResultType;
use App\GraphQL\Types\Input\SelectedAttributeInput;
use App\GraphQL\Types\Input\OrderItemInput;
use App\GraphQL\Types\OrderInputType;

class TypeRegistry
{
    private static array $types = [];

    public static function category(): CategoryType
    {
        return self::get(CategoryType::class);
    }

    public static function currency(): CurrencyType
    {
        return self::get(CurrencyType::class);
    }

    public static function price(): PriceType
    {
        return self::get(PriceType::class);
    }

    public static function product(): ProductType
    {
        return self::get(ProductType::class);
    }

    public static function galleryItem(): GalleryItemType
    {
        return self::get(GalleryItemType::class);
    }

    public static function attributeItem(): AttributeItemType
    {
        return self::get(AttributeItemType::class);
    }

    public static function attributeSet(): AttributeSetType
    {
        return self::get(AttributeSetType::class);
    }

    // --- Mutation Types ---
    public static function orderResult(): OrderResultType
    {
        return self::get(OrderResultType::class);
    }

    public static function selectedAttributeInput(): SelectedAttributeInput
    {
        return self::get(SelectedAttributeInput::class);
    }

    public static function orderItemInput(): OrderItemInput
    {
        return self::get(OrderItemInput::class);
    }

    public static function orderInput(): OrderInputType
    {
        return self::get(OrderInputType::class);
    }


    /**
     * Generic getter for any type. Ensures only one instance is created.
     *
     * @template T of \GraphQL\Type\Definition\Type
     * @param class-string<T> $classname
     * @return T
     */
    private static function get(string $classname): object
    {
        if (!isset(self::$types[$classname])) {
            self::$types[$classname] = new $classname();
        }
        return self::$types[$classname];
    }
}