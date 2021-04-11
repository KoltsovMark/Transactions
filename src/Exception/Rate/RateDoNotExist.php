<?php

declare(strict_types=1);

namespace CommissionTask\Exception\Rate;

use Exception;

class RateDoNotExist extends Exception
{
    protected $message = 'Rate do not exist';
}
