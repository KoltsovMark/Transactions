<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use CommissionTask\Exception\RateDoNotExist as RateDoNotExistException;
use CommissionTask\Factory\Rate as RateFactory;
use PHPUnit\Framework\TestCase;

use CommissionTask\Factory\Currency as CurrencyFactory;
use CommissionTask\Repository\Currency as CurrencyRepository;
use CommissionTask\Service\Rate as RateService;
use CommissionTask\Service\Currency as CurrencyService;

class CurrencyTest extends TestCase
{
    protected RateService $rateServiceMock;
    protected CurrencyRepository $currencyRepositoryMock;
    protected CurrencyFactory $currencyFactoryMock;
    protected CurrencyService $currencyService;
    protected RateFactory $rateFactory;

    /**
     * @covers \CommissionTask\Service\Currency::convertCurrency
     *
     * @dataProvider dataProviderForConvertCurrencyToTheSameCurrency
     */
    public function testConvertCurrencyToTheSameCurrency(
        string $amount,
        string $baseCurrencyCode,
        string $quoteCurrencyCode,
        string $expectation
    ) {
        $result = $this->currencyService->convertCurrency($amount, $baseCurrencyCode, $quoteCurrencyCode);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @return \string[][]
     */
    public function dataProviderForConvertCurrencyToTheSameCurrency()
    {
        return [
            'convert natural number EUR to EUR' => ['10', 'EUR', 'EUR', '10.00'],
            'convert float number EUR to EUR' => ['10.05', 'EUR', 'EUR', '10.05'],
            'convert float number EUR to EUR with rounding' => ['10.0523', 'EUR', 'EUR', '10.06'],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::convertCurrency
     *
     * @dataProvider dataProviderForConvertCurrencyToTheDifferentCurrency
     */
    public function testConvertCurrencyToTheDifferentCurrency(
        string $amount,
        string $baseCurrencyCode,
        string $quoteCurrencyCode,
        string $rate,
        string $expectation
    ) {
        $expectedRate = $this->rateFactory->create($baseCurrencyCode, $quoteCurrencyCode, $rate);

        $this->rateServiceMock
            ->expects($this->once())
            ->method('getRateByCodesOrTrow')
            ->with(...[$baseCurrencyCode, $quoteCurrencyCode])
            ->willReturn($expectedRate)
        ;

        $result = $this->currencyService->convertCurrency($amount, $baseCurrencyCode, $quoteCurrencyCode);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @return \string[][]
     */
    public function dataProviderForConvertCurrencyToTheDifferentCurrency()
    {
        return [
            'convert natural number EUR to USD' => ['10', 'EUR', 'USD', '1.1497', '11.50'],
            'convert float number EUR to USD with rounding' => ['10.578', 'EUR', 'USD', '1.1497', '12.17'],
            'convert float number USD to EUR' => ['11.50', 'USD', 'EUR', '0.86979212', '10.01'],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::minus
     *
     * @dataProvider dataProviderForMinusTheSameCurrency
     */
    public function testMinusTheSameCurrency(
        string $baseCurrencyAmonut,
        string $quoteCurrencyAmonut,
        string $currencyCode,
        string $expectation
    ) {
        $result = $this->currencyService->minus($baseCurrencyAmonut, $currencyCode, $quoteCurrencyAmonut, $currencyCode);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @return \string[][]
     */
    public function dataProviderForMinusTheSameCurrency()
    {
        return [
            'minus 2 natural numbers' => ['10', '5', 'EUR', '5.00'],
            'minus 2 float number' => ['10.0578', '2.123', 'EUR', '7.93'],
            'minus positive number from a negative' => ['-2.06', '1.05123', 'EUR', '-3.12'],
            'minus natural number from a float number' => ['2.067', '1', 'EUR', '1.07'],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::minus
     *
     * @dataProvider dataProviderForMinusTheDifferentCurrencies
     */
    public function testMinusTheDifferentCurrencies(
        string $baseCurrencyAmonut,
        string $baseCurrencyCode,
        string $quoteCurrencyAmonut,
        string $quoteCurrencyCode,
        string $expectedConvertedAmount,
        string $expectation
    ) {
        $currencyServicePartialMock = $this->createPartialMock(
            CurrencyService::class,
            ['convertCurrency']
        );

        $currencyServicePartialMock
            ->expects($this->once())
            ->method('convertCurrency')
            ->with(...[$quoteCurrencyAmonut, $quoteCurrencyCode, $baseCurrencyCode])
            ->willReturn($expectedConvertedAmount)
        ;

        $result = $currencyServicePartialMock->minus(
            $baseCurrencyAmonut,
            $baseCurrencyCode,
            $quoteCurrencyAmonut,
            $quoteCurrencyCode
        );

        $this->assertEquals($expectation, $result);
    }

    /**
     * @return \string[][]
     */
    public function dataProviderForMinusTheDifferentCurrencies()
    {
        return [
            'minus 2 natural numbers' => ['10', 'EUR', '5', 'USD', '4.35', '5.65'],
            'minus 2 float number' => ['10.0578', 'EUR', '2.123', 'USD', '1.07', '8.99'],
            'minus positive number from a negative' => ['-2.06', 'EUR', '1.05123', 'USD', '0.93', '-2.99'],
            'minus natural number from a float number' => ['2.067', 'EUR', '1', 'USD', '0.87', '1.20'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->rateServiceMock = $this->createMock(RateService::class);
        $this->currencyRepositoryMock = $this->createMock(CurrencyRepository::class);
        $this->currencyFactoryMock = $this->createMock(CurrencyFactory::class);
        $this->currencyService = new CurrencyService(
            $this->rateServiceMock,
            $this->currencyRepositoryMock,
            $this->currencyFactoryMock
        );
        $this->rateFactory = new RateFactory();
    }
}