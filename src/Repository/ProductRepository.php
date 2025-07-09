<?php

namespace App\Repository;

use App\Model\Product\Product;
use App\Model\Product\GalleryItem;
use App\Model\Price;
use App\Model\Currency;
use App\Model\Attribute\AbstractAttribute;
use App\Model\Attribute\TextAttribute;
use App\Model\Attribute\SwatchAttribute;
use App\Model\Attribute\AttributeItem;
use Doctrine\DBAL\Exception;
use Brick\Math\BigDecimal;
class ProductRepository extends AbstractRepository
{
    private CategoryRepository $categoryRepository;
    private CurrencyRepository $currencyRepository;

    public function __construct()
    {
        parent::__construct();
        $this->categoryRepository = new CategoryRepository();
        $this->currencyRepository = new CurrencyRepository();
    }

    /**
     * Finds a product by its ID and hydrates all its related data.
     *
     * @param string $id The product ID (VARCHAR).
     * @return Product|null
     * @throws Exception
     */
    public function findById(string $id): ?Product
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $productData = $queryBuilder
            ->select(
                'p.id as product_id', 'p.name', 'p.in_stock',
                'p.description', 'p.brand', 'p.category_id'
            )
            ->from('products', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (!$productData) {
            return null;
        }

        return $this->hydrateProduct($productData);
    }

    /**
     * Finds all products and hydrates their related data.
     *
     * @return Product[]
     * @throws Exception
     */
    public function findAll(): array
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $productsData = $queryBuilder
            ->select(
                'p.id as product_id', 'p.name', 'p.in_stock',
                'p.description', 'p.brand', 'p.category_id'
            )
            ->from('products', 'p')
            ->fetchAllAssociative();

        $products = [];
        foreach ($productsData as $productData) {
            $products[] = $this->hydrateProduct($productData);
        }

        return $products;
    }

    /**
     * Hydrates a single Product object with all its associated data.
     *
     * @param array $productData Associative array of product base data.
     * @return Product
     * @throws Exception
     */
    private function hydrateProduct(array $productData): Product
    {
        $product = new Product();
        $product->setProductId($productData['product_id']);
        $product->setName($productData['name']);
        $product->setInStock((bool)$productData['in_stock']);
        $product->setDescription($productData['description']);
        $product->setBrand($productData['brand']);


        // Hydrate Category
        $category = $this->categoryRepository->findById($productData['category_id']);
        $product->setCategory($category);

        // Hydrate Gallery Items
        $galleryItems = $this->findGalleryItemsByProductId($productData['product_id']);
        $product->setGallery($galleryItems);

        // Hydrate Prices
        $prices = $this->findPricesByProductId($productData['product_id']);
        $product->setPrices($prices);

        // Hydrate Attribute Sets (with polymorphism)
        $attributeSets = $this->findAttributeSetsByProductId($productData['product_id']);
        $product->setAttributeSets($attributeSets);

        return $product;
    }

    /**
     * Finds gallery items for a given product ID.
     *
     * @param string $productId
     * @return GalleryItem[]
     * @throws Exception
     */
    private function findGalleryItemsByProductId(string $productId): array
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $results = $queryBuilder
            ->select('id', 'image_url')
            ->from('product_galleries')
            ->where('product_id = :product_id')
            ->setParameter('product_id', $productId)
            ->fetchAllAssociative();

        $galleryItems = [];
        foreach ($results as $result) {
            $item = new GalleryItem();
            $item->setId($result['id']);
            $item->setImageUrl($result['image_url']);
            $galleryItems[] = $item;
        }
        return $galleryItems;
    }

    /**
     * Finds prices for a given product ID.
     *
     * @param string $productId
     * @return Price[]
     * @throws Exception
     */
    private function findPricesByProductId(string $productId): array
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $results = $queryBuilder
            ->select('p.id', 'p.amount', 'p.currency_id', 'c.label', 'c.symbol')
            ->from('prices', 'p')
            ->join('p', 'currencies', 'c', 'p.currency_id = c.id')
            ->where('p.product_id = :product_id')
            ->setParameter('product_id', $productId)
            ->fetchAllAssociative();

        $prices = [];
        foreach ($results as $result) {
            $currency = new Currency();
            $currency->setId($result['currency_id']);
            $currency->setLabel($result['label']);
            $currency->setSymbol($result['symbol']);

            $price = new Price();
            $price->setId($result['id']);
            // Convert to Decimal object
            $price->setAmount(BigDecimal::of($result['amount']));
            $price->setCurrency($currency);
            $prices[] = $price;
        }
        return $prices;
    }

    /**
     * Finds attribute sets and their items for a given product ID, handling polymorphism.
     *
     * @param string $productId
     * @return AbstractAttribute[]
     * @throws Exception
     */
    private function findAttributeSetsByProductId(string $productId): array
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $results = $queryBuilder
            ->select(
                'ats.id as attribute_set_id', 'ats.name as attribute_set_name', 'ats.type',
                'ati.id as item_id', 'ati.display_value', 'ati.value'
            )
            ->from('attribute_sets', 'ats')
            ->leftJoin('ats', 'attribute_items', 'ati', 'ats.id = ati.attribute_set_id')
            ->where('ats.product_id = :product_id')
            ->setParameter('product_id', $productId)
            ->orderBy('ats.id', 'ASC')
            ->orderBy('ati.id', 'ASC') 
            ->fetchAllAssociative();

        $attributeSets = [];
        $currentAttributeSetId = null;
        $currentAttributeSet = null;

        foreach ($results as $row) {
            // New attribute set
            if ($row['attribute_set_id'] !== $currentAttributeSetId) {
                if ($currentAttributeSet !== null) {
                    $attributeSets[] = $currentAttributeSet;
                }
                $currentAttributeSetId = $row['attribute_set_id'];

                // Polymorphic instantiation based on 'type'
                $currentAttributeSet = $this->createAttributeSetByType($row['type']);
                $currentAttributeSet->setId($row['attribute_set_id']);
                $currentAttributeSet->setName($row['attribute_set_name']);
                $currentAttributeSet->setType($row['type']);
            }

            // Add attribute item if it exists (left join might have nulls)
            if ($row['item_id'] !== null) {
                $item = new AttributeItem();
                $item->setId($row['item_id']);
                $item->setDisplayValue($row['display_value']);
                $item->setValue($row['value']);
                $currentAttributeSet->addItem($item);
            }
        }

        // Add the last attribute set if loop finished
        if ($currentAttributeSet !== null) {
            $attributeSets[] = $currentAttributeSet;
        }

        return $attributeSets;
    }

    /**
     * Factory method for polymorphic attribute instantiation.
     *
     * @param string $type The attribute type string (e.g., 'text', 'swatch').
     * @return AbstractAttribute
     * @throws \InvalidArgumentException If an unknown attribute type is encountered.
     */
    private function createAttributeSetByType(string $type): AbstractAttribute
    {
        switch ($type) {
            case 'text':
                return new TextAttribute();
            case 'swatch':
                return new SwatchAttribute();
            default:
                throw new \InvalidArgumentException("Unknown attribute type: {$type}");
        }
    }


    public function findByCategoryName(string $categoryName): array
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $productsData = $queryBuilder
            ->select(
                'p.id as product_id', 'p.name', 'p.in_stock',
                'p.description', 'p.brand', 'p.category_id'
            )
            ->from('products', 'p')
            ->join('p', 'categories', 'c', 'p.category_id = c.id')
            ->where('c.name = :categoryName')
            ->setParameter('categoryName', $categoryName)
            ->fetchAllAssociative();

        $products = [];
        foreach ($productsData as $productData) {
            $products[] = $this->hydrateProduct($productData);
        }

        return $products;
    }
}