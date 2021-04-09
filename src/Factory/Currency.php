<?php

declare(strict_types=1);

namespace CommissionTask\Factory;

use CommissionTask\Model\Currency as CurrencyModel;

class Currency
{
    public function createEmpty(): CurrencyModel
    {
        return new CurrencyModel();
    }

    public function create(string $currencyCode): CurrencyModel
    {
        return $this->createEmpty()->setCode($currencyCode);
    }
}
