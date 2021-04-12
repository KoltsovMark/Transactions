<?php

declare(strict_types=1);

namespace CommissionTask\Exception;

use Exception;

class EntryNotFound extends Exception
{
    protected $message = 'Entry not found';
}
