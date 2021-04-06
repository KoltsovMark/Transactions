<?php

declare(strict_types=1);

namespace CommissionTask\Exception;

use Exception;

class SourceOfTransactionsDoNotExist extends Exception
{
    protected $message = 'Provided source of transactions do not exist';
}