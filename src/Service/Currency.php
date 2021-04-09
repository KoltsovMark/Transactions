<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\ExchangeRateProvider\ConfigurableProvider;
use Brick\Money\Money;
use CommissionTask\Factory\Currency as CurrencyFactory;
use CommissionTask\Model\Currency as CurrencyModel;
use CommissionTask\Repository\Currency as CurrencyRepository;
use CommissionTask\Service\Rate as RateService;

class Currency
{
    public const ROUNDING_MODE = RoundingMode::UP;

    private RateService $rateService;
    private CurrencyRepository $currencyRepository;
    private CurrencyFactory $currencyFactory;

    /**
     * Currency constructor.
     *
     * @param Rate $rateService
     */
    public function __construct(
        RateService $rateService,
        CurrencyRepository $currencyRepository,
        CurrencyFactory $currencyFactory
    ) {
        $this->rateService = $rateService;
        $this->currencyRepository = $currencyRepository;
        $this->currencyFactory = $currencyFactory;

        $this->loadCurrencies();
    }

    /**
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\RateDoNotExist
     */
    public function minus(
        string $baseCurrencyAmount,
        string $baseCurrencyCode,
        string $quoteCurrencyAmount,
        string $quoteCurrencyCode
    ): string {
        $baseMoney = Money::of($baseCurrencyAmount, $baseCurrencyCode, null, self::ROUNDING_MODE);

        if ($baseCurrencyCode !== $quoteCurrencyCode) {
            $quoteCurrencyAmount = $this->convertCurrency($quoteCurrencyAmount, $quoteCurrencyCode, $baseCurrencyCode);
            $quoteCurrencyCode = $baseCurrencyCode;
        }

        $quoteMoney = Money::of($quoteCurrencyAmount, $quoteCurrencyCode, null, self::ROUNDING_MODE);

        return (string) $baseMoney->minus($quoteMoney)->getAmount();
    }

    /**
     * @return string
     *
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\RateDoNotExist
     */
    public function add(
        string $baseCurrencyAmount,
        string $baseCurrencyCode,
        string $quoteCurrencyAmount,
        string $quoteCurrencyCode
    ) {
        $baseMoney = Money::of($baseCurrencyAmount, $baseCurrencyCode, null, self::ROUNDING_MODE);

        if ($baseCurrencyCode !== $quoteCurrencyCode) {
            $quoteCurrencyAmount = $this->convertCurrency($quoteCurrencyAmount, $quoteCurrencyCode, $baseCurrencyCode);
            $quoteCurrencyCode = $baseCurrencyCode;
        }

        $quoteMoney = Money::of($quoteCurrencyAmount, $quoteCurrencyCode, null, self::ROUNDING_MODE);

        return (string) $baseMoney->plus($quoteMoney)->getAmount();
    }

    /**
     * @return string
     *
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function getFeePercentageForCurrency(string $amount, string $percents, string $currencyCode)
    {
        $money = Money::of($amount, $currencyCode, null, self::ROUNDING_MODE);

        return (string) $money->multipliedBy($percents, self::ROUNDING_MODE)
            ->dividedBy(100, self::ROUNDING_MODE)
            ->getAmount()
        ;
    }

    /**
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\RateDoNotExist
     */
    public function convertCurrency(string $amount, string $baseCurrencyCode, string $quoteCurrencyCode): string
    {
        $provider = new ConfigurableProvider();

        if ($baseCurrencyCode === $quoteCurrencyCode) {
            $provider->setExchangeRate($baseCurrencyCode, $baseCurrencyCode, 1);
        } else {
            $rate = $this->rateService->getRateByCodesOrTrow($baseCurrencyCode, $quoteCurrencyCode);
            $provider->setExchangeRate($baseCurrencyCode, $quoteCurrencyCode, $rate->getRate());
        }

        $converter = new CurrencyConverter($provider);
        $money = Money::of($amount, $baseCurrencyCode, null, self::ROUNDING_MODE);
        $convertedMoney = $converter->convert($money, $quoteCurrencyCode, self::ROUNDING_MODE);

        return (string) $convertedMoney->getAmount();
    }

    /**
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function getEmptyAmount(string $currencyCode): string
    {
        return (string) Money::of(0, $currencyCode)->getAmount();
    }

    /**
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\RateDoNotExist
     */
    public function isGreaterThan(
        string $baseCurrencyAmount,
        string $baseCurrencyCode,
        string $quoteCurrencyAmount,
        string $quoteCurrencyCode
    ): bool {
        $baseMoney = Money::of($baseCurrencyAmount, $baseCurrencyCode, null, self::ROUNDING_MODE);

        if ($baseCurrencyCode !== $quoteCurrencyCode) {
            $quoteCurrencyAmount = $this->convertCurrency($quoteCurrencyAmount, $quoteCurrencyCode, $baseCurrencyCode);
            $quoteCurrencyCode = $baseCurrencyCode;
        }

        $quoteMoney = Money::of($quoteCurrencyAmount, $quoteCurrencyCode, null, self::ROUNDING_MODE);

        return $baseMoney->isGreaterThan($quoteMoney);
    }

    /**
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\RateDoNotExist
     */
    public function isGreaterThanOrEqual(
        string $baseCurrencyAmount,
        string $baseCurrencyCode,
        string $quoteCurrencyAmount,
        string $quoteCurrencyCode
    ): bool {
        $baseMoney = Money::of($baseCurrencyAmount, $baseCurrencyCode, null, self::ROUNDING_MODE);

        if ($baseCurrencyCode !== $quoteCurrencyCode) {
            $quoteCurrencyAmount = $this->convertCurrency($quoteCurrencyAmount, $quoteCurrencyCode, $baseCurrencyCode);
            $quoteCurrencyCode = $baseCurrencyCode;
        }

        $quoteMoney = Money::of($quoteCurrencyAmount, $quoteCurrencyCode, null, self::ROUNDING_MODE);

        return $baseMoney->isGreaterThanOrEqualTo($quoteMoney);
    }

    /**
     * @param $amount
     * @param $currency
     *
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function isPositive($amount, $currency): bool
    {
        $money = Money::of($amount, $currency, null, self::ROUNDING_MODE);

        return $money->isPositive();
    }

    /**
     * @return string[]
     */
    private function getSupportedCurrenciesCodes(): array
    {
        return [
            CurrencyModel::EUR,
            CurrencyModel::USD,
            CurrencyModel::JPY,
        ];
    }

    /**
     * Load default currencies to repository if rate do not exist.
     */
    private function loadCurrencies(): void
    {
        foreach (self::getSupportedCurrenciesCodes() as $currencyCode) {
            $currency = $this->currencyFactory->create($currencyCode);
            $this->currencyRepository->add($currency);
        }
    }
}
