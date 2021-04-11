<?php

declare(strict_types=1);

namespace CommissionTask\Exception\Transaction;

use Exception;

class SourceOfTransactionsDoNotMatchFormat extends Exception
{
    protected $message = 'Source of transactions do not match the expected format';
}
