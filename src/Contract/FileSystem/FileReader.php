<?php

declare(strict_types=1);

namespace CommissionTask\Contract\FileSystem;

interface FileReader
{
    public function isSupportedFile(string $path): bool;
}
