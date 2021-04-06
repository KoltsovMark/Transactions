<?php

declare(strict_types=1);

namespace CommissionTask\Exception;

use Exception;

class FunctionalInDevelopment extends Exception
{
    protected $message = 'Functional in development';
}