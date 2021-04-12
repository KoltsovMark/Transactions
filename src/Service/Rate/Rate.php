<?php

declare(strict_types=1);

namespace CommissionTask\Service\Rate;

use CommissionTask\Exception\Rate\RateDoNotExist as RateDoNotExistException;
use CommissionTask\Factory\Rate\Rate as RateFactory;
use CommissionTask\Model\Rate\Rate as RateModel;
use CommissionTask\Repository\Rate\Rate as RateRepository;
use CommissionTask\Service\Configuration as ConfigurationService;
use CommissionTask\Service\Math as MathService;

class Rate
{
    private ConfigurationService $configurationService;
    private MathService $mathService;
    private RateRepository $rateRepository;
    private RateFactory $rateFactory;

    /**
     * Rate constructor. Load default rates to the system.
     */
    public function __construct(
        ConfigurationService $configurationService,
        MathService $mathService,
        RateRepository $rateRepository,
        RateFactory $rateFactory
    ) {
        $this->configurationService = $configurationService;
        $this->mathService = $mathService;
        $this->rateRepository = $rateRepository;
        $this->rateFactory = $rateFactory;

        if ($this->getDefaultRatesArray()) {
            $this->loadRates();
            $this->loadReversedRates();
        }
    }

    public function getDefaultRatesArray(): array
    {
        return $this->configurationService->get('rates') ?? [];
    }

    public function isRateSupported(string $baseCurrency, string $quoteCurrency): bool
    {
        return (bool) $this->rateRepository->getRateByCodesOrNull($baseCurrency, $quoteCurrency);
    }

    /**
     * @throws RateDoNotExistException
     */
    public function getRateByCodesOrTrow(string $baseCurrency, string $quoteCurrency): RateModel
    {
        if ($this->isRateSupported($baseCurrency, $quoteCurrency)) {
            return $this->rateRepository->getRateByCodesOrNull($baseCurrency, $quoteCurrency);
        } else {
            throw new RateDoNotExistException();
        }
    }

    /**
     * Load default rates to repository if rate do not exist.
     */
    private function loadRates(): void
    {
        foreach ($this->getDefaultRatesArray() as $baseCurrency => $quoteCurrencies) {
            foreach ($quoteCurrencies as $quoteCurrency => $rate) {
                $rateModel = $this->rateFactory->create($baseCurrency, $quoteCurrency, $rate);
                $this->rateRepository->add($rateModel);
            }
        }
    }

    /**
     * Calculate and load reversed rates if rate do not exist.
     */
    private function loadReversedRates()
    {
        foreach ($this->getDefaultRatesArray() as $baseCurrency => $quoteCurrencies) {
            foreach ($quoteCurrencies as $quoteCurrency => $rate) {
                $rateModel = $this->rateFactory
                    ->create(
                        $quoteCurrency,
                        $baseCurrency,
                        $this->mathService->divide('1', $rate)
                    )
                ;
                $this->rateRepository->add($rateModel);
            }
        }
    }
}
