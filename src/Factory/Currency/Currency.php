<?php

declare(strict_types=1);

namespace CommissionTask\Factory\Currency;

use CommissionTask\Model\Currency\Currency as CurrencyModel;

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
