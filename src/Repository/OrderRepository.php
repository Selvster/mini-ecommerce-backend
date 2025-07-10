<?php

namespace App\Repository;

use App\Model\Order\Order;
use App\Model\Order\OrderItem;
use App\Model\Currency;
use Doctrine\DBAL\Exception;
use Brick\Math\BigDecimal;

class OrderRepository extends AbstractRepository
{
    private CurrencyRepository $currencyRepository;


    public function __construct()
    {
        parent::__construct();
        $this->currencyRepository = new CurrencyRepository();
    }

    /**
     * Finds all orders and hydrates their related data.
     *
     * @return Order[]
     * @throws Exception
     */
    public function findAll(): array
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $results = $queryBuilder
            ->select(
                'o.id as order_id', 'o.created_at', 'o.total_amount', 'o.currency_id',
                'c.label as currency_label', 'c.symbol as currency_symbol'
            )
            ->from('orders', 'o')
            ->join('o', 'currencies', 'c', 'o.currency_id = c.id')
            ->orderBy('o.created_at', 'DESC')
            ->fetchAllAssociative();

        $orders = [];
        foreach ($results as $row) {
            $order = $this->hydrateOrder($row);
            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * Finds a single order by its ID.
     *
     * @param int $id
     * @return Order|null
     * @throws Exception
     */
    public function findById(int $id): ?Order
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $result = $queryBuilder
            ->select(
                'o.id as order_id', 'o.created_at', 'o.total_amount', 'o.currency_id',
                'c.label as currency_label', 'c.symbol as currency_symbol'
            )
            ->from('orders', 'o')
            ->join('o', 'currencies', 'c', 'o.currency_id = c.id')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (!$result) {
            return null;
        }

        return $this->hydrateOrder($result);
    }


    /**
     * Hydrates an Order object with its items and related data.
     *
     * @param array $orderData
     * @return Order
     * @throws Exception
     */
    private function hydrateOrder(array $orderData): Order
    {
        $currency = new Currency();
        $currency->setId($orderData['currency_id']);
        $currency->setLabel($orderData['currency_label']);
        $currency->setSymbol($orderData['currency_symbol']);

        $order = new Order();
        $order->setId($orderData['order_id']);
        $order->setCreatedAt(new \DateTimeImmutable($orderData['created_at']));
        $order->setTotalAmount(BigDecimal::of($orderData['total_amount']));
        $order->setCurrency($currency);

        $orderItems = $this->findOrderItemsByOrderId($orderData['order_id']);
        $order->setItems($orderItems);

        return $order;
    }

    /**
     * Finds order items for a given order ID.
     *
     * @param int $orderId
     * @return OrderItem[]
     * @throws Exception
     */
    private function findOrderItemsByOrderId(int $orderId): array
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $results = $queryBuilder
            ->select('oi.id', 'oi.order_id', 'oi.product_id', 'oi.quantity', 'oi.selected_attributes')
            ->from('order_items', 'oi')
            ->where('oi.order_id = :orderId')
            ->setParameter('orderId', $orderId)
            ->fetchAllAssociative();

        $orderItems = [];
        foreach ($results as $row) {
            $item = new OrderItem();
            $item->setId($row['id']);
            $item->setOrderId($row['order_id']);
            $item->setProductId($row['product_id']);
            $item->setQuantity($row['quantity']);
            $item->setSelectedAttributes(json_decode($row['selected_attributes'], true)); 

            $orderItems[] = $item;
        }
        return $orderItems;
    }
}