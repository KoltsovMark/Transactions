<?php

declare(strict_types=1);

namespace CommissionTask\Factory\FileSystem;

use CommissionTask\Contract\FileSystem\IterableFileReader as IterableFileReaderInterface;
use CommissionTask\Exception\FileSystem\UnsupportedFileExtension as UnsupportedFileExtensionException;
use CommissionTask\Service\FileSystem\CsvReader as CsvReaderService;

class FileReader
{
    private CsvReaderService $csvReaderService;

    public function __construct(CsvReaderService $csvReaderService)
    {
        $this->csvReaderService = $csvReaderService;
    }

    public function createIterable(string $path): IterableFileReaderInterface
    {
        if ($this->csvReaderService->isSupportedFile($path)) {
            return new CsvReaderService();
        } else {
            throw new UnsupportedFileExtensionException();
        }
    }
}
