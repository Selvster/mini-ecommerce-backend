<?php

namespace App\Repository;

use App\Model\Category;
use Doctrine\DBAL\Exception;

class CategoryRepository extends AbstractRepository
{
    /**
     * Finds a category by its ID.
     *
     * @param int $id The category ID.
     * @return Category|null
     * @throws Exception
     */
    public function findById(int $id): ?Category
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $result = $queryBuilder
            ->select('id', 'name')
            ->from('categories')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (!$result) {
            return null;
        }

        $category = new Category();
        $category->setId($result['id']);
        $category->setName($result['name']);

        return $category;
    }

    /**
     * Finds a category by its name.
     *
     * @param string $name The category name.
     * @return Category|null
     * @throws Exception
     */
    public function findByName(string $name): ?Category
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $result = $queryBuilder
            ->select('id', 'name')
            ->from('categories')
            ->where('name = :name')
            ->setParameter('name', $name)
            ->fetchAssociative();

        if (!$result) {
            return null;
        }

        $category = new Category();
        $category->setId($result['id']);
        $category->setName($result['name']);

        return $category;
    }

    /**
     * Finds all categories.
     *
     * @return Category[]
     * @throws Exception
     */
    public function findAll(): array
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $results = $queryBuilder
            ->select('id', 'name')
            ->from('categories')
            ->fetchAllAssociative();

        $categories = [];
        foreach ($results as $result) {
            $category = new Category();
            $category->setId($result['id']);
            $category->setName($result['name']);
            $categories[] = $category;
        }

        return $categories;
    }
}