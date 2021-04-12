<?php

declare(strict_types=1);

namespace CommissionTask\Contract\FileSystem;

use CommissionTask\Exception\FileSystem\UnsupportedFileExtension as UnsupportedFileExtensionException;

interface IterableFileReader
{
    /**
     * @throws UnsupportedFileExtensionException
     */
    public function readByRaws(string $path): iterable;
}
