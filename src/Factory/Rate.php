<?php

declare(strict_types=1);

namespace CommissionTask\Factory;

use CommissionTask\Model\Rate as RateModel;

class Rate
{
    /**
     * @return RateModel
     */
    public function createEmpty(): RateModel
    {
        return new RateModel();
    }

    /**
     * @param string $baseCurrency
     * @param string $quoteCurrency
     * @param string $rate
     *
     * @return RateModel
     */
    public function create(
        string $baseCurrency,
        string $quoteCurrency,
        string $rate
    ): RateModel
    {
        return $this->createEmpty()
            ->setBaseCurrency($baseCurrency)
            ->setQuoteCurrency($quoteCurrency)
            ->setRate($rate)
        ;
    }
}