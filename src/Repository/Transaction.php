<?php

namespace CommissionTask\Repository;
use CommissionTask\Contract\Repository;
use CommissionTask\Model\Transaction as TransactionModel;

class Transaction implements Repository
{
    /**
     * @var TransactionModel[]
     */
    protected $transactions = [];

    /**
     * @return TransactionModel[]
     */
    public function getAll()
    {
        return $this->transactions;
    }

    public function add(TransactionModel $transaction)
    {
        $this->transactions[] = $transaction;
    }
}