<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\OrderResultType;     
use App\GraphQL\Types\OrderInputType;       
use App\Repository\ProductRepository;
use App\Repository\CurrencyRepository;
use App\Database\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Brick\Math\BigDecimal;
use Throwable;
use App\GraphQL\TypeRegistry;
class MutationType extends ObjectType
{
    private ProductRepository $productRepository;
    private CurrencyRepository $currencyRepository;
    private $dbConnection;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->currencyRepository = new CurrencyRepository();
        $this->dbConnection = Connection::getInstance();

        parent::__construct([
            'name' => 'Mutation',
            'fields' => [
                'placeOrder' => [
                    'type' => Type::nonNull(TypeRegistry::orderResult()),
                    'description' => 'Places a new order in the system',
                    'args' => [
                        'input' => Type::nonNull(TypeRegistry::orderInput()),
                    ],
                    'resolve' => function ($rootValue, array $args): array {
                        $orderItemsInput = $args['input']['items'];
                        $totalAmount = BigDecimal::zero();
                        $defaultCurrency = null;

                        if (empty($orderItemsInput)) {
                            throw new \InvalidArgumentException('Order must contain at least one item.');
                        }

                        $this->dbConnection->beginTransaction();

                        try {
                            $defaultCurrency = $this->currencyRepository->findById(1);

                            if (!$defaultCurrency) {
                                throw new \RuntimeException('Default currency not found.');
                            }

                            $orderData = [
                                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                                'total_amount' => '0.00',
                                'currency_id' => $defaultCurrency->getId(),
                            ];
                            $this->dbConnection->insert('orders', $orderData);
                            $orderId = (int)$this->dbConnection->lastInsertId();

                            foreach ($orderItemsInput as $itemInput) {
                                $productId = $itemInput['productId'];
                                $quantity = $itemInput['quantity'];
                                $selectedAttributes = json_encode($itemInput['selectedAttributes']);

                                $product = $this->productRepository->findById($productId);

                                if (!$product || !$product->isInStock()) {
                                    throw new \RuntimeException("Product {$productId} not found or out of stock.");
                                }

                                $productPrice = null;
                                foreach ($product->getPrices() as $price) {
                                    if ($price->getCurrency()->getLabel() === $defaultCurrency->getLabel()) {
                                        $productPrice = $price->getAmount();
                                        break;
                                    }
                                }

                                if (!$productPrice) {
                                    throw new \RuntimeException("Price for product {$productId} in {$defaultCurrency->getLabel()} not found.");
                                }

                                $itemTotal = $productPrice->multipliedBy(BigDecimal::of($quantity));
                                $totalAmount = $totalAmount->plus($itemTotal);

                                $orderItemData = [
                                    'order_id' => $orderId,
                                    'product_id' => $productId,
                                    'quantity' => $quantity,
                                    'selected_attributes' => $selectedAttributes,
                                ];
                                $this->dbConnection->insert('order_items', $orderItemData);
                            }

                            $this->dbConnection->update(
                                'orders',
                                ['total_amount' => $totalAmount->toScale(2)],
                                ['id' => $orderId]
                            );

                            $this->dbConnection->commit();

                            return [
                                'orderId' => $orderId,
                                'totalAmount' => $totalAmount->toScale(2),
                                'currency' => $defaultCurrency,
                                'message' => 'Order placed successfully!',
                            ];

                        } catch (DBALException $e) {
                            $this->dbConnection->rollBack();
                            throw new \RuntimeException('Database error during order placement: ' . $e->getMessage(), 0, $e);
                        } catch (Throwable $e) {
                            $this->dbConnection->rollBack();
                            throw new \RuntimeException('Failed to place order: ' . $e->getMessage(), 0, $e);
                        }
                    }
                ],
            ],
        ]);
    }
}