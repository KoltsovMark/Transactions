<?php

namespace CommissionTask\Service;

use CommissionTask\Model\Rate as RateModel;

class Rate
{
    protected const DEFAULT_RATES = [
      'EUR' => [
          'USD' => '1.1497',
          'JPY' => '129.53'
      ]
    ];

    /**
     * @var RateModel[]
     */
    protected array $rates = [];

    public function __construct()
    {
        $this->loadRates();
    }

    /**
     * @return \string[][]
     */
    protected static function getDefaultRatesArray(): array
    {
        return self::DEFAULT_RATES;
    }

    protected function loadRates(): void
    {
        foreach (Rate::getDefaultRatesArray() as $baseCurrency => $quoteCurrencies) {
            foreach ($quoteCurrencies as $quoteCurrency => $rate) {
                $this->addRate($baseCurrency, $quoteCurrency, $rate);
            }
        }
    }

    /**
     * @return RateModel[]
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @param string $baseCurrency
     * @param string $quoteCurrency
     *
     * @return RateModel|null
     */
    public function getRateByCodesOrNull(string $baseCurrency, string $quoteCurrency): ?RateModel
    {
        foreach ($this->getRates() as $rate) {
            if ($rate->getBaseCurrency() === $baseCurrency && $rate->getQuoteCurrency() === $quoteCurrency) {
                return $rate;
            }
        }

        return null;
    }

    /**
     * @param string $baseCurrency
     * @param string $quoteCurrency
     * @param string $rate
     *
     * @return $this
     */
    public function addRate(string $baseCurrency, string $quoteCurrency, string $rate): Rate
    {
        $rateModel = $this->getRateByCodesOrNull($baseCurrency, $quoteCurrency);

        if (is_null($rateModel)) {
            $this->rates[] = new RateModel($baseCurrency, $quoteCurrency, $rate);
        }

        return $this;
    }

    /**
     * @param string $baseCurrency
     * @param string $quoteCurrency
     *
     * @return bool
     */
    public function isRateSupported(string $baseCurrency, string $quoteCurrency): bool
    {
        return (bool) $this->getRateByCodesOrNull($baseCurrency, $quoteCurrency);
    }
}