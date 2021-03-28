<?php

namespace CommissionTask\Service;

use CommissionTask\Model\Currency as CurrencyModel;

class Currency
{
    public const EUR = 'EUR';
    public const USD = 'USD';
    public const JPY = 'JPY';

    /**
     * @var CurrencyModel[]
     */
    protected array $currencies = [];

    public function __construct()
    {
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
            $this->addCurrency($currencyCode);
        }
    }

    /**
     * @param string $currencyCode
     *
     * @return CurrencyModel|null
     */
    public function getCurrencyByCodeOrNull(string $currencyCode): ?CurrencyModel
    {
        foreach ($this->getCurrencies() as $currency) {
            if ($currency->getCode() === $currencyCode) {
                return $currency;
            }
        }

        return null;
    }

    /**
     * @param string $currencyCode
     *
     * @return $this
     */
    public function addCurrency(string $currencyCode): Currency
    {
        $currency = $this->getCurrencyByCodeOrNull($currencyCode);

        if (empty($currency)) {
            $this->currencies[] = new CurrencyModel($currencyCode);
        }

        return $this;
    }

    /**
     * @return CurrencyModel[]
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    /**
     * @param string $currencyCode
     *
     * @return bool
     */
    public function isSupportedCurrency(string $currencyCode): bool
    {
        return (bool) $this->getCurrencyByCodeOrNull($currencyCode);
    }
}