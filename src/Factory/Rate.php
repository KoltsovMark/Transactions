<?php

declare(strict_types=1);

namespace CommissionTask\Factory;

use CommissionTask\Model\Rate as RateModel;

class Rate
{
    public function createEmpty(): RateModel
    {
        return new RateModel();
    }

    public function create(
        string $baseCurrency,
        string $quoteCurrency,
        string $rate
    ): RateModel {
        return $this->createEmpty()
            ->setBaseCurrency($baseCurrency)
            ->setQuoteCurrency($quoteCurrency)
            ->setRate($rate)
        ;
    }
}
