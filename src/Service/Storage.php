<?php

namespace CommissionTask\Service;

use CommissionTask\Model\Currency;
use CommissionTask\Model\Rate;
use CommissionTask\Service\Currency as CurrencyService;
use CommissionTask\Service\Rate as RateService;

class Storage
{
    /**
     * @var Rate[]
     */
    protected array $rates = [];
    /**
     * @var Currency[]
     */
    protected array $currencies = [];

    public function loadData(): void
    {
        $this->loadCurrencies();
        $this->loadRates();
    }

    public function loadCurrencies(): void
    {
        foreach (CurrencyService::getSupportedCurrencies() as $currencyCode) {
            $this->addCurrency($currencyCode);
        }
    }

    public function loadRates(): void
    {
        foreach (RateService::getDefaultRatesArray() as $baseCurrency => $quoteCurrencies) {
            foreach ($quoteCurrencies as $quoteCurrency => $rate) {
                $this->addRate($baseCurrency, $quoteCurrency, $rate);
            }
        }
    }

    /**
     * @return Rate[]
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @param Rate[] $rates
     *
     * @return Storage
     */
    public function setRates(array $rates): Storage
    {
        $this->rates = $rates;
        return $this;
    }

    /**
     * @return Currency[]
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    /**
     * @param Currency[] $currencies
     *
     * @return Storage
     */
    public function setCurrencies(array $currencies): Storage
    {
        $this->currencies = $currencies;
        return $this;
    }

    public function addCurrency(string $currencyCode): Storage
    {
        $filteredCurrencies = \array_filter($this->getCurrencies(), function (Currency $currency) use ($currencyCode) {
            return $currency->getCode() === $currencyCode;
        });

        if (empty($filteredCurrencies)) {
            $this->currencies[] = new Currency($currencyCode);
        }

        return $this;
    }

    public function addRate(string $baseCurrency, string $quoteCurrency, string $rate): Storage
    {
        $filteredRates = \array_filter($this->getRates(), function (Rate $rate) use ($baseCurrency, $quoteCurrency) {
            return $rate->getBaseCurrency() === $baseCurrency && $rate->getQuoteCurrency() === $quoteCurrency;
        });

        if (empty($filteredRates)) {
            $this->rates[] = new Rate($baseCurrency, $quoteCurrency, $rate);
        }

        return $this;
    }
}