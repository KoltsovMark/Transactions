<?php

declare(strict_types=1);

namespace CommissionTask\Factory;

use CommissionTask\Model\Currency as CurrencyModel;

class Currency
{
    /**
     * @return CurrencyModel
     */
    public function createEmpty(): CurrencyModel
    {
        return new CurrencyModel();
    }

    /**
     * @param string $currencyCode
     *
     * @return CurrencyModel
     */
    public function create(string $currencyCode): CurrencyModel
    {
        return $this->createEmpty()->setCode($currencyCode);
    }
}