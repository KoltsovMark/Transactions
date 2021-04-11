<?php

declare(strict_types=1);

namespace CommissionTask\Exception\Transaction;

use Exception;

class SourceOfTransactionsDoNotExist extends Exception
{
    protected $message = 'Provided source of transactions do not exist';
}
