<?php

declare(strict_types=1);

namespace CommissionTask\Repository\Transaction;

use CommissionTask\Contract\Repository as RepositoryInterface;
use CommissionTask\Model\Transaction\Transaction as TransactionModel;
use DateTime;

class Transaction implements RepositoryInterface
{
    protected static $instance;

    /**
     * @var TransactionModel[]
     */
    private array $transactions = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return TransactionModel[]
     */
    public function getAll(): array
    {
        return $this->transactions;
    }

    public function add(TransactionModel $transaction)
    {
        $this->transactions[] = $transaction;
    }

    /**
     * @return TransactionModel[]
     */
    public function getCashOutByCustomerIdAndTransactionDate(
        int $customerId,
        DateTime $startDate,
        DateTime $endDate
    ): array {
        return \array_filter($this->getAll(),
            function (TransactionModel $transactionModel) use ($customerId, $startDate, $endDate) {
                return $transactionModel->getCustomer()->getId() === $customerId
                    && $transactionModel->isCashOut()
                    && $transactionModel->getCreatedAt() >= $startDate
                    && $transactionModel->getCreatedAt() <= $endDate
                ;
            });
    }
}
