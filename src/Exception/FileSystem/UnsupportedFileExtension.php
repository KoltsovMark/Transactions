<?php

declare(strict_types=1);

namespace CommissionTask\Exception\FileSystem;

use Exception;

class UnsupportedFileExtension extends Exception
{
    protected $message = 'Unsupported file extension';
}
