<?php

declare(strict_types=1);

namespace CommissionTask\Service\Transaction;

use CommissionTask\Dto\Transaction\NewTransaction as NewTransactionDto;
use CommissionTask\Exception\FunctionalInDevelopment as FunctionalInDevelopmentException;
use CommissionTask\Factory\Commission\TransactionCommission as TransactionCommissionFactory;
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
    private TransactionCommissionFactory $transactionCommissionFactory;
    private TransactionFactory $transactionFactory;
    private CustomerFactory $customerFactory;
    private CurrencyFactory $currencyFactory;

    /**
     * Transaction constructor.
     */
    public function __construct(
        CommissionService $commissionService,
        TransactionRepository $transactionRepository,
        TransactionCommissionFactory $transactionCommissionFactory,
        TransactionFactory $transactionFactory,
        CustomerFactory $customerFactory,
        CurrencyFactory $currencyFactory
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->commissionService = $commissionService;
        $this->transactionCommissionFactory = $transactionCommissionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->customerFactory = $customerFactory;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @throws FunctionalInDevelopmentException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function processTransaction(NewTransactionDto $newTransactionDto): TransactionModel
    {
        $transactionCommissionDto = $this->transactionCommissionFactory->createFromNewTransactionDto($newTransactionDto);
        $transaction = $this->transactionFactory->createFromNewTransactionDto($newTransactionDto);
        $customer = $this->customerFactory->createFromNewTransactionDto($newTransactionDto);
        $currency = $this->currencyFactory->create($newTransactionDto->getCurrencyCode());

        $transaction->setCustomer($customer);
        $transaction->setCurrency($currency);

        if ($transaction->isCashIn()) {
            $commission = $this->commissionService->calculateCashInCommission($transactionCommissionDto);
        } elseif ($transaction->isCashOut()) {
            if ($customer->isLegalPerson()) {
                $commission = $this->commissionService->calculateCashOutLegalCommission($transactionCommissionDto);
            } elseif ($customer->isNaturalPerson()) {
                $commission = $this->commissionService->calculateCashOutNaturalCommission($transactionCommissionDto);
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
