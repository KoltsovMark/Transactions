<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use CommissionTask\Exception\RateDoNotExist as RateDoNotExistException;
use CommissionTask\Factory\Rate as RateFactory;
use CommissionTask\Model\Rate as RateModel;
use CommissionTask\Repository\Rate as RateRepository;
use CommissionTask\Service\Math as MathService;

class Rate
{
    protected const DEFAULT_RATES = [
      'EUR' => [
          'USD' => '1.1497',
          'JPY' => '129.53'
      ]
    ];

    protected MathService $mathService;
    protected RateRepository $rateRepository;
    protected RateFactory $rateFactory;

    /**
     * Rate constructor. Load default rates to the system.
     *
     * @param Math $mathService
     * @param RateRepository $rateRepository
     * @param RateFactory $rateFactory
     */
    public function __construct(
        MathService $mathService,
        RateRepository $rateRepository,
        RateFactory $rateFactory
    ) {
        $this->mathService = $mathService;
        $this->rateRepository = $rateRepository;
        $this->rateFactory = $rateFactory;

        $this->loadRates();
        $this->loadReversedRates();
    }

    /**
     * @return \string[][]
     */
    protected static function getDefaultRatesArray(): array
    {
        return self::DEFAULT_RATES;
    }

    /**
     * Load default rates to repository if rate do not exist
     */
    protected function loadRates(): void
    {
        foreach (Rate::getDefaultRatesArray() as $baseCurrency => $quoteCurrencies) {
            foreach ($quoteCurrencies as $quoteCurrency => $rate) {
                $rateModel = $this->rateFactory->create($baseCurrency, $quoteCurrency, $rate);
                $this->rateRepository->addRate($rateModel);
            }
        }
    }

    /**
     * Calculate and load reversed rates if rate do not exist
     */
    protected function loadReversedRates()
    {
        foreach (Rate::getDefaultRatesArray() as $baseCurrency => $quoteCurrencies) {
            foreach ($quoteCurrencies as $quoteCurrency => $rate) {
                $rateModel = $this->rateFactory
                    ->create(
                        $quoteCurrency,
                        $baseCurrency,
                        $this->mathService->divide('1', $rate)
                    )
                ;
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

    /**
     * @param string $baseCurrency
     * @param string $quoteCurrency
     *
     * @return RateModel
     * @throws RateDoNotExistException
     */
    public function getRateByCodesOrTrow(string $baseCurrency, string $quoteCurrency): RateModel
    {
        $rate = $this->rateRepository->getRateByCodesOrNull($baseCurrency, $quoteCurrency);

        if (\is_null($rate)) {
            throw new RateDoNotExistException();
        }

        return $rate;
    }
}