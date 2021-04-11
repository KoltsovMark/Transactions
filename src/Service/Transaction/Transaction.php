<?php

declare(strict_types=1);

namespace CommissionTask\Service\Transaction;

use Carbon\Carbon;
use CommissionTask\Model\Transaction\Transaction as TransactionModel;
use CommissionTask\Repository\Transaction\Transaction as TransactionRepository;

class Transaction
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public static function isSupportedTransactionType(string $transactionType): bool
    {
        return \in_array($transactionType, [TransactionModel::TYPE_CASH_OUT, TransactionModel::TYPE_CASH_IN], true);
    }

    public function getWeeklyCashOutTransactionsByCustomerAndDate(int $customerId, string $transactionDate): array
    {
        $transactionDate = new Carbon($transactionDate);
        $startOfWeek = (new Carbon($transactionDate))->startOfWeek();
        $endOfWeek = (new Carbon($transactionDate))->endOfWeek();

        return $this->transactionRepository->getCashOutByCustomerIdAndTransactionDate(
            $customerId,
            $startOfWeek,
            $endOfWeek
        );
    }
}
