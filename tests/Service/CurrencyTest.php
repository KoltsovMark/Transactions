<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use CommissionTask\Factory\Rate as RateFactory;
use CommissionTask\Model\Currency as CurrencyModel;
use PHPUnit\Framework\TestCase;

use CommissionTask\Factory\Currency as CurrencyFactory;
use CommissionTask\Repository\Currency as CurrencyRepository;
use CommissionTask\Service\Rate as RateService;
use CommissionTask\Service\Currency as CurrencyService;

//@todo add tests on exceptions
class CurrencyTest extends TestCase
{
    protected RateService $rateServiceMock;
    protected CurrencyRepository $currencyRepositoryMock;
    protected CurrencyFactory $currencyFactoryMock;
    protected CurrencyService $currencyService;
    protected RateFactory $rateFactory;

    /**
     * @covers \CommissionTask\Service\Currency::getSupportedCurrenciesCodes
     */
    public function testGetSupportedCurrenciesCodes()
    {
       $supportedCurrencies = [
           CurrencyModel::EUR,
           CurrencyModel::USD,
           CurrencyModel::JPY,
       ];

       $this->assertEquals($supportedCurrencies, CurrencyService::getSupportedCurrenciesCodes());
    }

    /**
     * @covers \CommissionTask\Service\Currency::isSupportedCurrencyCode
     *
     * @dataProvider dataProviderForIsSupportedCurrencyCode
     */
    public function testIsSupportedCurrencyCode(string $currencyCode, bool $expectation)
    {
        $this->assertEquals($expectation, CurrencyService::isSupportedCurrencyCode($currencyCode));
    }

    /**
     * @return array[]
     */
    public function dataProviderForIsSupportedCurrencyCode()
    {
        return [
            'supported currency' => ['USD', true],
            'unsupported currency' => ['UAH', false],
        ];
    }

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
            'convert float number JPY to JPY with rounding' => ['10.01', 'JPY', 'JPY', '11'],
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

    /**
     * @covers \CommissionTask\Service\Currency::add
     *
     * @dataProvider dataProviderForAddTheSameCurrency
     */
    public function testAddTheSameCurrency(
        string $baseCurrencyAmonut,
        string $quoteCurrencyAmonut,
        string $currencyCode,
        string $expectation
    ) {
        $result = $this->currencyService->add($baseCurrencyAmonut, $currencyCode, $quoteCurrencyAmonut, $currencyCode);

        $this->assertEquals($expectation, $result);
    }

    /**
     * @return \string[][]
     */
    public function dataProviderForAddTheSameCurrency()
    {
        return [
            'add 2 natural numbers' => ['10', '5', 'EUR', '15.00'],
            'add 2 float number' => ['10.0578', '2.123', 'EUR', '12.19'],
            'add positive number from a negative' => ['-2.06', '1.05123', 'EUR', '-1.00'],
            'add natural number from a float number' => ['2.067', '1', 'EUR', '3.07'],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::add
     *
     * @dataProvider dataProviderForAddTheDifferentCurrencies
     */
    public function testAddTheDifferentCurrencies(
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

        $result = $currencyServicePartialMock->add(
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
    public function dataProviderForAddTheDifferentCurrencies()
    {
        return [
            'minus 2 natural numbers' => ['10', 'EUR', '5', 'USD', '4.35', '14.35'],
            'minus 2 float number' => ['10.0578', 'EUR', '2.123', 'USD', '1.07', '11.13'],
            'minus positive number from a negative' => ['-2.06', 'EUR', '1.05123', 'USD', '0.93', '-1.13'],
            'minus natural number from a float number' => ['2.067', 'EUR', '1', 'USD', '0.87', '2.94'],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::getFeePercentageForCurrency
     *
     * @dataProvider dataProviderForGetFeePercentageForCurrency
     */
    public function testGetFeePercentageForCurrency(
        string $amount,
        string $percents,
        string $currencyCode,
        string $expectation
    ) {
        $this->assertEquals(
            $expectation,
            $this->currencyService->getFeePercentageForCurrency($amount, $percents, $currencyCode)
        );
    }

    /**
     * @return \string[][]
     */
    public function dataProviderForGetFeePercentageForCurrency()
    {
        return [
            'percentage of natural number with natural percentage' => ['10', '1', 'EUR', '0.10'],
            'percentage of float number with natural percentage' => ['10.567', '1', 'EUR', '0.11'],
            'percentage of natural negative number with natural percentage' => ['-10', '1', 'EUR', '-0.10'],
            'percentage of float negative number with natural percentage' => ['-10.567', '1', 'EUR', '-0.11'],
            'percentage of natural number with float percentage' => ['10', '0.03', 'EUR', '0.01'],
            'percentage of float number with float percentage' => ['10.567', '0.03', 'EUR', '0.01'],
            'percentage of natural negative number with float percentage' => ['-10', '0.3', 'EUR', '-0.03'],
            'percentage of float negative number with float percentage' => ['-10.567', '0.3', 'EUR', '-0.04'],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::getEmptyAmount
     *
     * @dataProvider dataProviderForGetEmptyAmount
     */
    public function testGetEmptyAmount(string $currencyCode, string $expectation)
    {
        $this->assertEquals($expectation, $this->currencyService->getEmptyAmount($currencyCode));
    }

    /**
     * @return \string[][]
     */
    public function dataProviderForGetEmptyAmount()
    {
        return [
            'get empty ammount for EUR' => ['EUR', '0.00'],
            'get empty ammount for JPY' => ['JPY', '0'],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::isGreaterThan
     *
     * @dataProvider dataProviderForIsGreaterThanTheSameCurrencies
     */
    public function testIsGreaterThanTheSameCurrencies(
        string $baseCurrencyAmonut,
        string $quoteCurrencyAmonut,
        string $currencyCode,
        bool $expectation
    ) {


        $this->assertEquals($expectation, $this->currencyService->isGreaterThan(
            $baseCurrencyAmonut,
            $currencyCode,
            $quoteCurrencyAmonut,
            $currencyCode
        ));
    }

    /**
     * @return array[]
     */
    public function dataProviderForIsGreaterThanTheSameCurrencies()
    {
        return [
            'natural number 1 is greater then natural number 2 with the same currencies' => ['4', '3', 'EUR', true],
            'natural number 1 is equal with natural number 2 with the same currencies' => ['4', '4', 'EUR', false],
            'natural number 1 is less then natural number 2 with the same currencies' => ['3', '4', 'EUR', false],
            'float number 1 is greater then float number 2 with the same currencies' => ['4.221', '4.22', 'EUR', true],
            'natural number 1 is greater then float number 2 with the same currencies' => ['4', '3.989', 'EUR', true],
            'natural number 1 is equal with float number 2 with the same currencies' => ['4', '3.999', 'EUR', false],
            'natural number 1 is less then float number 2 with the same currencies' => ['3', '3.001', 'EUR', false],
            'natural number 1 is greater then negative float number 2 with the same currencies' => ['4', '-3.989', 'EUR', true],
            'natural negative number 1 is less then negative float number 2 with the same currencies' => ['-4', '-3.50', 'EUR', false],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::isGreaterThan
     *
     * @dataProvider dataProviderForIsGreaterThanTheDifferentCurrencies
     */
    public function testIsGreaterThanTheDifferentCurrencies(
        string $baseCurrencyAmonut,
        string $baseCurrencyCode,
        string $quoteCurrencyAmonut,
        string $quoteCurrencyCode,
        string $expectedConvertedAmount,
        bool $expectation
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

        $result = $currencyServicePartialMock->isGreaterThan(
            $baseCurrencyAmonut,
            $baseCurrencyCode,
            $quoteCurrencyAmonut,
            $quoteCurrencyCode
        );

        $this->assertEquals($expectation, $result);
    }

    /**
     * @return array[]
     */
    public function dataProviderForIsGreaterThanTheDifferentCurrencies()
    {
        return [
            'natural number 1 in EUR is greater then natural number 2 in USD' => ['4', 'EUR', '3', 'USD', '2.61', true],
            'natural number 1 in EUR is equal with float number 2 in USD' => ['4', 'EUR', '4.60', 'USD', '4', false],
            'natural number 1 in EUR is less then float number 2 in USD' => ['3', 'EUR', '4.60', 'USD', '4', false],
            'natural number 1 in EUR is greater then negative float number 2 in USD' => ['4', 'EUR', '-3', 'USD', '-2.61', true],
            'natural negative number 1 in EUR is less then negative float number 2 in USD' => ['-4', 'EUR', '-3', 'USD', '-2.61', false],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::isGreaterThanOrEqual
     *
     * @dataProvider dataProviderForIsGreaterThanOrEqualTheSameCurrencies
     */
    public function testisGreaterThanOrEqualTheSameCurrencies(
        string $baseCurrencyAmonut,
        string $quoteCurrencyAmonut,
        string $currencyCode,
        bool $expectation
    ) {


        $this->assertEquals($expectation, $this->currencyService->isGreaterThanOrEqual(
            $baseCurrencyAmonut,
            $currencyCode,
            $quoteCurrencyAmonut,
            $currencyCode
        ));
    }

    /**
     * @return array[]
     */
    public function dataProviderForIsGreaterThanOrEqualTheSameCurrencies()
    {
        return [
            'natural number 1 is greater then natural number 2 with the same currencies' => ['4', '3', 'EUR', true],
            'natural number 1 is equal with natural number 2 with the same currencies' => ['4', '4', 'EUR', true],
            'natural number 1 is less then natural number 2 with the same currencies' => ['3', '4', 'EUR', false],
            'float number 1 is greater then float number 2 with the same currencies' => ['4.221', '4.22', 'EUR', true],
            'natural number 1 is greater then float number 2 with the same currencies' => ['4', '3.989', 'EUR', true],
            'natural number 1 is equal with float number 2 with the same currencies' => ['4', '3.999', 'EUR', true],
            'natural number 1 is less then float number 2 with the same currencies' => ['3', '3.001', 'EUR', false],
            'natural number 1 is greater then negative float number 2 with the same currencies' => ['4', '-3.989', 'EUR', true],
            'natural negative number 1 is less then negative float number 2 with the same currencies' => ['-4', '-3.50', 'EUR', false],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::isGreaterThanOrEqual
     *
     * @dataProvider dataProviderForIsGreaterThanOrEqualTheDifferentCurrencies
     */
    public function testisGreaterThanOrEqualTheDifferentCurrencies(
        string $baseCurrencyAmonut,
        string $baseCurrencyCode,
        string $quoteCurrencyAmonut,
        string $quoteCurrencyCode,
        string $expectedConvertedAmount,
        bool $expectation
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

        $result = $currencyServicePartialMock->isGreaterThanOrEqual(
            $baseCurrencyAmonut,
            $baseCurrencyCode,
            $quoteCurrencyAmonut,
            $quoteCurrencyCode
        );

        $this->assertEquals($expectation, $result);
    }

    /**
     * @return array[]
     */
    public function dataProviderForIsGreaterThanOrEqualTheDifferentCurrencies()
    {
        return [
            'natural number 1 in EUR is greater then natural number 2 in USD' => ['4', 'EUR', '3', 'USD', '2.61', true],
            'natural number 1 in EUR is equal with float number 2 in USD' => ['4', 'EUR', '4.60', 'USD', '4', true],
            'natural number 1 in EUR is less then float number 2 in USD' => ['3', 'EUR', '4.60', 'USD', '4', false],
            'natural number 1 in EUR is greater then negative float number 2 in USD' => ['4', 'EUR', '-3', 'USD', '-2.61', true],
            'natural negative number 1 in EUR is less then negative float number 2 in USD' => ['-4', 'EUR', '-3', 'USD', '-2.61', false],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Currency::isPositive
     *
     * @dataProvider dataProviderForIsPositive
     */
    public function testIsPositive(string $amount, string $currencyCode, bool $expectation)
    {
        $this->assertEquals($expectation, $this->currencyService->isPositive($amount, $currencyCode));
    }

    /**
     * @return array[]
     */
    public function dataProviderForIsPositive()
    {
        return [
            'positive with natural number' => ['4', 'EUR', true],
            'negative with natural number' => ['-4', 'EUR', false],
            'positive with float number' => ['4.05', 'EUR', true],
            'negative with float number' => ['-4.05', 'EUR', false],
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