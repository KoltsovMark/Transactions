<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use CommissionTask\Factory\TransactionCommission as TransactionCommissionFactory;
use CommissionTask\Service\Commission as CommissionService;
use CommissionTask\Service\Currency as CurrencyService;
use CommissionTask\Service\Transaction as TransactionService;
use PHPUnit\Framework\TestCase;

class CommissionTest extends TestCase
{
    protected const CASH_IN_FEE = '0.03';
    protected const CASH_IN_MAX_FEE = '5.00';

    protected const CASH_OUT_LEGAL_FEE = '0.3';

    protected CommissionService $commissionService;
    protected CurrencyService $currencyServiceMock;
    protected TransactionService $transactionServiceMock;
    protected TransactionCommissionFactory $transactionCommissionFactory;

    /**
     * @covers \CommissionTask\Service\Commission::calculateCashInCommission
     *
     * @dataProvider dataProviderForCalculateCashInCommission
     */
    public function testCalculateCashInCommission(string $amount, string $currencyCode, string $expectation)
    {
        $transactionCommissionDto = $this->transactionCommissionFactory
            ->createEmpty()
            ->setAmount($amount)
            ->setCurrencyCode($currencyCode)
        ;

        $commissionServicePartialMock = $this->createPartialMock(
            CommissionService::class,
            ['calculateFeePercentage', 'applyCashInMaxLimitCheck']
        );

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
            $commissionServicePartialMock->calculateCashInCommission($transactionCommissionDto)
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
        $transactionCommissionDto = $this->transactionCommissionFactory
            ->createEmpty()
            ->setAmount($transactionAmount)
            ->setCurrencyCode($transactionCurrency)
            ->setCreatedAt($transactionDate)
            ->setCustomerId($customerId)
        ;

        $commissionServicePartialMock = $this->createPartialMock(
            CommissionService::class,
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
           $commissionServicePartialMock->calculateCashOutNaturalCommission($transactionCommissionDto)
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
        $transactionCommissionDto = $this->transactionCommissionFactory
            ->createEmpty()
            ->setAmount($transactionAmount)
            ->setCurrencyCode($transactionCurrency)
        ;

        $commissionServicePartialMock = $this->createPartialMock(
            CommissionService::class,
            ['calculateFeePercentage', 'applyCashOutLegalMinLimitCheck']
        );

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
            $commissionServicePartialMock->calculateCashOutLegalCommission($transactionCommissionDto)
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

        $this->currencyServiceMock = $this->createMock(CurrencyService::class);
        $this->transactionServiceMock = $this->createMock(TransactionService::class);
        $this->commissionService = new CommissionService(
            $this->currencyServiceMock,
            $this->transactionServiceMock
        );
        $this->transactionCommissionFactory = new TransactionCommissionFactory();
    }
}