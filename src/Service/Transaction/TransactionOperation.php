<?php

declare(strict_types=1);

namespace CommissionTask\Service\Transaction;

use CommissionTask\Dto\Transaction\NewTransaction as NewTransactionDto;
use CommissionTask\Exception\FunctionalInDevelopment as FunctionalInDevelopmentException;
use CommissionTask\Factory\Commission\CashInCommission as CashInCommissionFactory;
use CommissionTask\Factory\Commission\CashOutLegalCommission as CashOutLegalCommissionFactory;
use CommissionTask\Factory\Commission\CashOutNaturalCommission as CashOutNaturalCommissionFactory;
use CommissionTask\Factory\Currency\Currency as CurrencyFactory;
use CommissionTask\Factory\Customer\Customer as CustomerFactory;
use CommissionTask\Factory\Transaction\Transaction as TransactionFactory;
use CommissionTask\Model\Transaction\Transaction as TransactionModel;
use CommissionTask\Repository\Transaction\Transaction as TransactionRepository;
use CommissionTask\Service\Commission\Commission as CommissionService;

class TransactionOperation
{
    private TransactionRepository $transactionRepository;
    private CommissionService $commissionService;
    private TransactionFactory $transactionFactory;
    private CustomerFactory $customerFactory;
    private CurrencyFactory $currencyFactory;
    private CashInCommissionFactory $cashInCommissionFactory;
    private CashOutLegalCommissionFactory $cashOutLegalCommissionFactory;

    /**
     * Transaction constructor.
     */
    public function __construct(
        CommissionService $commissionService,
        TransactionRepository $transactionRepository,
        TransactionFactory $transactionFactory,
        CustomerFactory $customerFactory,
        CurrencyFactory $currencyFactory,
        CashInCommissionFactory $cashInCommissionFactory,
        CashOutLegalCommissionFactory $cashOutLegalCommissionFactory,
        CashOutNaturalCommissionFactory $cashOutNaturalCommissionFactory
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->commissionService = $commissionService;
        $this->transactionFactory = $transactionFactory;
        $this->customerFactory = $customerFactory;
        $this->currencyFactory = $currencyFactory;
        $this->cashInCommissionFactory = $cashInCommissionFactory;
        $this->cashOutLegalCommissionFactory = $cashOutLegalCommissionFactory;
        $this->cashOutNaturalCommissionFactory = $cashOutNaturalCommissionFactory;
    }

    /**
     * @throws FunctionalInDevelopmentException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function processTransaction(NewTransactionDto $newTransactionDto): TransactionModel
    {
        $transaction = $this->transactionFactory->createFromNewTransactionDto($newTransactionDto);
        $customer = $this->customerFactory->createFromNewTransactionDto($newTransactionDto);
        $currency = $this->currencyFactory->create($newTransactionDto->getCurrencyCode());

        $transaction->setCustomer($customer);
        $transaction->setCurrency($currency);

        if ($transaction->isCashIn()) {
            $commissionDto = $this->cashInCommissionFactory->createFromNewTransactionDto($newTransactionDto);
            $commission = $this->commissionService->calculateCashInCommission($commissionDto);
        } elseif ($transaction->isCashOut()) {
            if ($customer->isLegalPerson()) {
                $commissionDto = $this->cashOutLegalCommissionFactory->createFromNewTransactionDto($newTransactionDto);
                $commission = $this->commissionService->calculateCashOutLegalCommission($commissionDto);
            } elseif ($customer->isNaturalPerson()) {
                $commissionDto = $this->cashOutNaturalCommissionFactory->createFromNewTransactionDto($newTransactionDto);
                $commission = $this->commissionService->calculateCashOutNaturalCommission($commissionDto);
            } else {
                throw new FunctionalInDevelopmentException();
            }
        } else {
            throw new FunctionalInDevelopmentException();
        }

        $transaction->setCommission($commission);

        $this->transactionRepository->add($transaction);

        return $transaction;
    }
}
