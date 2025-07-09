<?php

namespace App\Repository;

use App\Model\Currency;
use Doctrine\DBAL\Exception;

class CurrencyRepository extends AbstractRepository
{
    /**
     * Finds a currency by its ID.
     *
     * @param int $id The currency ID.
     * @return Currency|null
     * @throws Exception
     */
    public function findById(int $id): ?Currency
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $result = $queryBuilder
            ->select('id', 'label', 'symbol')
            ->from('currencies')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (!$result) {
            return null;
        }

        $currency = new Currency();
        $currency->setId($result['id']);
        $currency->setLabel($result['label']);
        $currency->setSymbol($result['symbol']);

        return $currency;
    }

    /**
     * Finds all currencies.
     *
     * @return Currency[]
     * @throws Exception
     */
    public function findAll(): array
    {
        $queryBuilder = $this->dbConnection->createQueryBuilder();
        $results = $queryBuilder
            ->select('id', 'label', 'symbol')
            ->from('currencies')
            ->fetchAllAssociative();

        $currencies = [];
        foreach ($results as $result) {
            $currency = new Currency();
            $currency->setId($result['id']);
            $currency->setLabel($result['label']);
            $currency->setSymbol($result['symbol']);
            $currencies[] = $currency;
        }

        return $currencies;
    }
}