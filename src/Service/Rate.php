<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use CommissionTask\Model\Rate as RateModel;
use CommissionTask\Repository\Rate as RateRepository;

class Rate
{
    protected const DEFAULT_RATES = [
      'EUR' => [
          'USD' => '1.1497',
          'JPY' => '129.53'
      ]
    ];

    /**
     * @var RateRepository
     */
    protected $rateRepository;

    public function __construct(RateRepository $rateRepository)
    {
        $this->rateRepository = $rateRepository;

        $this->loadRates();
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
                $rateModel = new RateModel($baseCurrency, $quoteCurrency, $rate);
                $this->rateRepository->addRate($rateModel);
            }
        }
    }

    /**
     * @param string $baseCurrency
     * @param string $quoteCurrency
     *
     * @return bool
     */
    public function isRateSupported(string $baseCurrency, string $quoteCurrency): bool
    {
        return (bool) $this->rateRepository->getRateByCodesOrNull($baseCurrency, $quoteCurrency);
    }
}