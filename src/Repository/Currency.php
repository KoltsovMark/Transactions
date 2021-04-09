<?php

declare(strict_types=1);

namespace CommissionTask\Repository;

use CommissionTask\Contract\Repository as RepositoryInterface;
use CommissionTask\Model\Currency as CurrencyModel;

class Currency implements RepositoryInterface
{
    /**
     * @var CurrencyModel[]
     */
    private array $currencies = [];

    /**
     * @return CurrencyModel[]
     */
    public function getAll(): array
    {
        return $this->currencies;
    }

    public function getCurrencyByCodeOrNull(string $currencyCode): ?CurrencyModel
    {
        foreach ($this->getAll() as $currency) {
            if ($currency->getCode() === $currencyCode) {
                return $currency;
            }
        }

        return null;
    }

    public function add(CurrencyModel $currency): Currency
    {
        if (is_null($this->getCurrencyByCodeOrNull($currency->getCode()))) {
            $this->currencies[] = $currency;
        }

        return $this;
    }
}
