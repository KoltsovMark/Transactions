<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use CommissionTask\Dto\NewTransaction as NewTransactionDto;
use CommissionTask\Exception\FunctionalInDevelopment as FunctionalInDevelopmentException;
use CommissionTask\Factory\Currency as CurrencyFactory;
use CommissionTask\Factory\Customer as CustomerFactory;
use CommissionTask\Factory\Transaction as TransactionFactory;
use CommissionTask\Factory\TransactionCommission as TransactionCommissionFactory;
use CommissionTask\Model\Transaction as TransactionModel;
use CommissionTask\Repository\Transaction as TransactionRepository;
use CommissionTask\Service\Commission as CommissionService;

class Transaction
{
    protected TransactionRepository $transactionRepository;
    protected CommissionService $commissionService;
    protected TransactionCommissionFactory $transactionCommissionFactory;
    protected TransactionFactory $transactionFactory;
    protected CustomerFactory $customerFactory;
    protected CurrencyFactory $currencyFactory;

    /**
     * Transaction constructor.
     *
     * @param Commission $commissionService
     * @param TransactionRepository $transactionRepository
     * @param TransactionCommissionFactory $transactionCommissionFactory
     * @param TransactionFactory $transactionFactory
     * @param CustomerFactory $customerFactory
     * @param CurrencyFactory $currencyFactory
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
     * @param NewTransactionDto $newTransactionDto
     *
     * @return TransactionModel
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