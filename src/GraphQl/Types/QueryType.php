<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Model\Category;
use App\Model\Product\Product;
use App\GraphQL\TypeRegistry; 

class QueryType extends ObjectType
{
    private CategoryRepository $categoryRepository;
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
        $this->productRepository = new ProductRepository();

        parent::__construct([
            'name' => 'Query',
            'fields' => [
                'categories' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::category()))),
                    'description' => 'Returns a list of all product categories',
                    'resolve' => function (): array {
                        return $this->categoryRepository->findAll();
                    }
                ],
                'products' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::product()))),
                    'description' => 'Returns a list of all products, optionally filtered by category',
                    'args' => [
                        'category' => Type::string(),
                    ],
                    'resolve' => function ($rootValue, array $args): array {
                        $categoryName = $args['category'] ?? null;

                        if ($categoryName && $categoryName !== 'all') {
                            return $this->productRepository->findByCategoryName($categoryName);
                        }
                        return $this->productRepository->findAll();
                    }
                ],
                'product' => [
                    'type' => TypeRegistry::product(),
                    'description' => 'Returns a single product by its ID',
                    'args' => [
                        'id' => Type::nonNull(Type::id()),
                    ],
                    'resolve' => function ($rootValue, array $args): ?Product {
                        return $this->productRepository->findById($args['id']);
                    }
                ],
            ],
        ]);
    }
}