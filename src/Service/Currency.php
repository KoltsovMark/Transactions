<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use CommissionTask\Model\Currency as CurrencyModel;
use CommissionTask\Repository\Currency as CurrencyRepository;

class Currency
{
    public const EUR = 'EUR';
    public const USD = 'USD';
    public const JPY = 'JPY';

    protected CurrencyRepository $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;

        $this->loadCurrencies();
    }

    /**
     * @return string[]
     */
    protected function getSupportedCurrenciesCodes(): array
    {
        return [
            self::EUR,
            self::USD,
            self::JPY,
        ];
    }

    protected function loadCurrencies(): void
    {
        foreach (self::getSupportedCurrenciesCodes() as $currencyCode) {
            $currency = new CurrencyModel($currencyCode);
            $this->currencyRepository->add($currency);
        }
    }

    /**
     * @param string $currencyCode
     *
     * @return bool
     */
    public function isSupportedCurrency(string $currencyCode): bool
    {
        return (bool) $this->currencyRepository->getCurrencyByCodeOrNull($currencyCode);
    }
}