<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use CommissionTask\Factory\Commission\CashInCommission as CashInCommissionFactory;
use CommissionTask\Factory\Commission\CashOutLegalCommission as CashOutLegalCommissionFactory;
use CommissionTask\Factory\Commission\CashOutNaturalCommission as CashOutNaturalCommissionFactory;
use CommissionTask\Factory\Currency\Currency as CurrencyFactory;
use CommissionTask\Factory\Customer\Customer as CustomerFactory;
use CommissionTask\Factory\Transaction\NewTransaction as NewTransactionFactory;
use CommissionTask\Factory\Transaction\Transaction as TransactionFactory;
use CommissionTask\Repository\Transaction\Transaction as TransactionRepository;
use CommissionTask\Service\Commission\Commission as CommissionService;
use CommissionTask\Service\Transaction\TransactionOperation as TransactionOperationService;
use DateTime;
use PHPUnit\Framework\TestCase;

class TransactionOperationTest extends TestCase
{
    protected TransactionOperationService $transactionOperationService;
    protected CommissionService $commissionServiceMock;
    protected TransactionRepository $transactionRepositoryMock;
    protected TransactionFactory $transactionFactoryMock;
    protected CustomerFactory $customerFactoryMock;
    protected CurrencyFactory $currencyFactoryMock;
    protected CashInCommissionFactory $cashInCommissionFactoryMock;
    protected CashOutLegalCommissionFactory $cashOutLegalCommissionFactoryMock;
    protected CashOutNaturalCommissionFactory $cashOutNaturalCommissionFactoryMock;

    /**
     * @covers \CommissionTask\Service\Transaction::processTransaction
     *
     * @dataProvider dataProviderForProcessTransaction
     */
    public function testProcessTransaction(
        string $amount,
        string $currencyCode,
        int $customerId,
        string $customerType,
        string $transactionType,
        string $createdAt,
        string $expectedMethod,
        string $expectedCommission
    ) {
        $newTransactionDto = (new NewTransactionFactory())->createFromArray([
                $createdAt,
                $customerId,
                $customerType,
                $transactionType,
                $amount,
                $currencyCode,
            ]
        );

        if ($expectedMethod === 'calculateCashInCommission') {
            $transactionCommissionDto = (new CashInCommissionFactory())->createFromNewTransactionDto($newTransactionDto);
            $commissionFactoryMock = $this->cashInCommissionFactoryMock;
        } elseif ($expectedMethod === 'calculateCashOutLegalCommission') {
            $transactionCommissionDto = (new CashOutLegalCommissionFactory())->createFromNewTransactionDto($newTransactionDto);
            $commissionFactoryMock = $this->cashOutLegalCommissionFactoryMock;
        } else {
            $transactionCommissionDto = (new CashOutNaturalCommissionFactory())->createFromNewTransactionDto($newTransactionDto);
            $commissionFactoryMock = $this->cashOutNaturalCommissionFactoryMock;
        }


        $transaction = (new TransactionFactory())->createFromNewTransactionDto($newTransactionDto);
        $customer = (new CustomerFactory())->createFromNewTransactionDto($newTransactionDto);
        $currency = (new CurrencyFactory())->create($newTransactionDto->getCurrencyCode());

        $commissionFactoryMock
            ->expects($this->once())
            ->method('createFromNewTransactionDto')
            ->with(...[$newTransactionDto])
            ->willReturn($transactionCommissionDto)
        ;

        $this->transactionFactoryMock
            ->expects($this->once())
            ->method('createFromNewTransactionDto')
            ->with(...[$newTransactionDto])
            ->willReturn($transaction)
        ;

        $this->customerFactoryMock
            ->expects($this->once())
            ->method('createFromNewTransactionDto')
            ->with(...[$newTransactionDto])
            ->willReturn($customer)
        ;

        $this->currencyFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(...[$currencyCode])
            ->willReturn($currency)
        ;

        $this->commissionServiceMock
            ->expects($this->once())
            ->method($expectedMethod)
            ->with($transactionCommissionDto)
            ->willReturn($expectedCommission)
        ;

        $this->transactionRepositoryMock
            ->expects($this->once())
            ->method('add')
            ->with($transaction)
        ;

        $newTransaction = $this->transactionOperationService->processTransaction($newTransactionDto);

        $this->assertSame($amount, $newTransaction->getAmount());
        $this->assertSame($expectedCommission, $newTransaction->getCommission());
        $this->assertEquals(new DateTime($createdAt), $newTransaction->getCreatedAt());
        $this->assertSame($currency, $newTransaction->getCurrency());
        $this->assertSame($customer, $newTransaction->getCustomer());
        $this->assertSame($transactionType, $newTransaction->getType());
    }

    /**
     * @return array[]
     */
    public function dataProviderForProcessTransaction()
    {
        return [
            'new cash in transaction of natural customer' => [
                '15.23',
                'EUR',
                2,
                'natural',
                'cash_in',
                '2017-10-21',
                'calculateCashInCommission',
                '10.00'
            ],
            'new cash in transaction of legal customer' => [
                '15.23',
                'EUR',
                2,
                'legal',
                'cash_in',
                '2017-10-21',
                'calculateCashInCommission',
                '10.00'
            ],
            'new cash out transaction of natural customer' => [
                '15.23',
                'EUR',
                2,
                'natural',
                'cash_out',
                '2017-10-21',
                'calculateCashOutNaturalCommission',
                '10.00'
            ],
            'new cash out transaction of legal customer' => [
                '15.23',
                'EUR',
                2,
                'legal',
                'cash_out',
                '2017-10-21',
                'calculateCashOutLegalCommission',
                '10.00'
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->transactionRepositoryMock = $this->createMock(TransactionRepository::class);
        $this->commissionServiceMock = $this->createMock(CommissionService::class);
        $this->transactionFactoryMock = $this->createMock(TransactionFactory::class);
        $this->customerFactoryMock = $this->createMock(CustomerFactory::class);
        $this->currencyFactoryMock = $this->createMock(CurrencyFactory::class);
        $this->cashInCommissionFactoryMock = $this->createMock(CashInCommissionFactory::class);
        $this->cashOutLegalCommissionFactoryMock = $this->createMock(CashOutLegalCommissionFactory::class);
        $this->cashOutNaturalCommissionFactoryMock = $this->createMock(CashOutNaturalCommissionFactory::class);
        $this->transactionOperationService = new TransactionOperationService(
            $this->commissionServiceMock,
            $this->transactionRepositoryMock,
            $this->transactionFactoryMock,
            $this->customerFactoryMock,
            $this->currencyFactoryMock,
            $this->cashInCommissionFactoryMock,
            $this->cashOutLegalCommissionFactoryMock,
            $this->cashOutNaturalCommissionFactoryMock
        );
    }
}