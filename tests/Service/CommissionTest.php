<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use CommissionTask\Factory\Commission\CashInCommission as CashInCommissionFactory;
use CommissionTask\Factory\Commission\CashOutLegalCommission as CashOutLegalCommissionFactory;
use CommissionTask\Factory\Commission\CashOutNaturalCommission as CashOutNaturalCommissionFactory;
use CommissionTask\Service\Commission\Commission as CommissionService;
use CommissionTask\Service\Configuration as ConfigurationService;
use CommissionTask\Service\Currency\Currency as CurrencyService;
use CommissionTask\Service\Transaction\Transaction as TransactionService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CommissionTest extends TestCase
{
    protected const CASH_IN_FEE = '0.03';
    protected const CASH_IN_MAX_FEE = '5.00';
    protected const CASH_IN_CONFIGURATION_PATH = 'commission.cash_in';
    protected const CASH_OUT_CONFIGURATION_PATH = 'commission.cash_out';

    protected const CASH_OUT_LEGAL_FEE = '0.3';

    public const COMMISSION_TYPE_PERCENTAGE = 1;
    public const COMMISSION_TYPE_VALUE = 2;
    public const COMMISSION_TYPE_RENEWAL_WEEKLY = 1;

    protected ConfigurationService $configurationServiceMock;
    protected CommissionService $commissionService;
    protected CurrencyService $currencyServiceMock;
    protected TransactionService $transactionServiceMock;
    protected CashInCommissionFactory $cashInCommissionFactory;
    protected CashOutLegalCommissionFactory $cashOutLegalCommissionFactory;
    protected CashOutNaturalCommissionFactory $cashOutNaturalCommissionFactory;

    /**
     * @covers \CommissionTask\Service\Commission::calculateCashInCommission
     *
     * @dataProvider dataProviderForCalculateCashInCommission
     */
    public function testCalculateCashInCommission(string $amount, string $currencyCode, string $expectation)
    {
        $cashInCommissionDto = $this->cashInCommissionFactory
            ->createEmpty()
            ->setAmount($amount)
            ->setCurrencyCode($currencyCode)
        ;

        $commissionServicePartialMock = $this->createCommissionServicePartialMock(
            ['calculateFeePercentage', 'applyCashInMaxLimitCheck']
        );

        $this->configurationServiceMock
            ->expects($this->at(0))
            ->method('get')
            ->with(...[self::CASH_IN_CONFIGURATION_PATH . '.fee.type'])
            ->willReturn(self::COMMISSION_TYPE_PERCENTAGE)
        ;

        $this->configurationServiceMock
            ->expects($this->at(1))
            ->method('get')
            ->with(...[self::CASH_IN_CONFIGURATION_PATH . '.fee.value'])
            ->willReturn(self::CASH_IN_FEE)
        ;

        $commissionServicePartialMock
            ->expects($this->once())
            ->method('calculateFeePercentage')
            ->with(...[$amount, self::CASH_IN_FEE, $currencyCode])
            ->willReturn($expectation)
        ;

        $commissionServicePartialMock
            ->expects($this->once())
            ->method('applyCashInMaxLimitCheck')
            ->with(...[$expectation, $currencyCode])
            ->willReturn($expectation)
        ;

        $this->assertEquals(
            $expectation,
            $commissionServicePartialMock->calculateCashInCommission($cashInCommissionDto)
        );
    }

    /**
     * @return \string[][]
     */
    public function dataProviderForCalculateCashInCommission()
    {
        return [
            'float number with a fee lower than max limit in EUR' => ['1.23', 'EUR', '0.01'],
            'float number with a fee greater than max limit in EUR' => ['2000000', 'EUR', '5.00'],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Commission::calculateCashOutNaturalCommission
     *
     * @dataProvider dataProviderForCalculateCashOutNaturalCommission
     */
    public function testCalculateCashOutNaturalCommission(
        string $transactionAmount,
        string $transactionCurrency,
        string $transactionDate,
        int $customerId,
        string $expectation
    ) {
        $cashOutNaturalCommissionDto = $this->cashOutNaturalCommissionFactory
            ->createEmpty()
            ->setAmount($transactionAmount)
            ->setCurrencyCode($transactionCurrency)
            ->setCreatedAt($transactionDate)
            ->setCustomerId($customerId)
        ;

        $commissionServicePartialMock = $this->createCommissionServicePartialMock(
            ['applyCashOutNaturalFreeOfChargeCheck']
        );

        $commissionServicePartialMock
            ->expects($this->once())
            ->method('applyCashOutNaturalFreeOfChargeCheck')
            ->with(...[$transactionAmount, $transactionCurrency, $transactionDate, $customerId])
            ->willReturn($expectation)
        ;

       $this->assertEquals(
           $expectation,
           $commissionServicePartialMock->calculateCashOutNaturalCommission($cashOutNaturalCommissionDto)
       );
    }

    /**
     * @return array[]
     */
    public function dataProviderForCalculateCashOutNaturalCommission()
    {
        return [
            'float number with a fee lower than max limit in EUR' => ['1.23', 'EUR', '2014-12-31', 1, '5.00'],
            'float number with a fee greater than max limit in USD' => ['2000000', 'USD', '2017-10-21', 2, '5.00'],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Commission::calculateCashOutLegalCommission
     *
     * @dataProvider dataProviderForCalculateCashOutLegalCommission
     */
    public function testCalculateCashOutLegalCommission(
        string $transactionAmount,
        string $transactionCurrency,
        string $expectation
    ) {
        $cashOutLegalCommissionDto = $this->cashOutLegalCommissionFactory
            ->createEmpty()
            ->setAmount($transactionAmount)
            ->setCurrencyCode($transactionCurrency)
        ;

        $commissionServicePartialMock = $this->createCommissionServicePartialMock(
            ['calculateFeePercentage', 'applyCashOutLegalMinLimitCheck']
        );

        $this->configurationServiceMock
            ->expects($this->at(0))
            ->method('get')
            ->with(...[self::CASH_OUT_CONFIGURATION_PATH . '.legal_person.fee.type'])
            ->willReturn(self::COMMISSION_TYPE_PERCENTAGE)
        ;

        $this->configurationServiceMock
            ->expects($this->at(1))
            ->method('get')
            ->with(...[self::CASH_OUT_CONFIGURATION_PATH . '.legal_person.fee.value'])
            ->willReturn(self::CASH_OUT_LEGAL_FEE)
        ;

        $commissionServicePartialMock
            ->expects($this->once())
            ->method('calculateFeePercentage')
            ->with(...[$transactionAmount, self::CASH_OUT_LEGAL_FEE, $transactionCurrency])
            ->willReturn($expectation)
        ;

        $commissionServicePartialMock
            ->expects($this->once())
            ->method('applyCashOutLegalMinLimitCheck')
            ->with(...[$expectation, $transactionCurrency])
            ->willReturn($expectation)
        ;

        $this->assertEquals(
            $expectation,
            $commissionServicePartialMock->calculateCashOutLegalCommission($cashOutLegalCommissionDto)
        );
    }

    /**
     * @return array[]
     */
    public function dataProviderForCalculateCashOutLegalCommission()
    {
        return [
            'float number with a fee lower than max limit in EUR' => ['1.23', 'EUR', '2014-12-31', 1],
            'float number with a fee greater than max limit in USD' => ['2000000', 'USD', '2017-10-21', 2],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->configurationServiceMock = $this->createMock(ConfigurationService::class);
        $this->currencyServiceMock = $this->createMock(CurrencyService::class);
        $this->transactionServiceMock = $this->createMock(TransactionService::class);
        $this->commissionService = new CommissionService(
            $this->configurationServiceMock,
            $this->currencyServiceMock,
            $this->transactionServiceMock
        );
        $this->cashInCommissionFactory = new CashInCommissionFactory();
        $this->cashOutLegalCommissionFactory = new CashOutLegalCommissionFactory();
        $this->cashOutNaturalCommissionFactory = new CashOutNaturalCommissionFactory();
    }

    protected function createCommissionServicePartialMock($methods = [])
    {
        $partialMock = $this->createPartialMock(
            CommissionService::class,
            $methods
        );

        $class = new ReflectionClass(CommissionService::class);
        $property = $class->getProperty('configurationService');
        $property->setAccessible(true);
        $property->setValue($partialMock, $this->configurationServiceMock);

        return $partialMock;
    }
}