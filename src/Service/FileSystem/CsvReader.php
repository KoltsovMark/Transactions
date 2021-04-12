<?php

declare(strict_types=1);

namespace CommissionTask\Service\FileSystem;

use CommissionTask\Contract\FileSystem\FileReader as FileReaderInterface;
use CommissionTask\Contract\FileSystem\IterableFileReader as IterableFileReaderInterface;
use CommissionTask\Exception\FileSystem\UnsupportedFileExtension as UnsupportedFileExtensionException;

class CsvReader implements FileReaderInterface, IterableFileReaderInterface
{
    protected const SUPPORTED_FORMATS = ['csv'];

    public function isSupportedFile(string $path): bool
    {
        return \in_array(pathinfo($path)['extension'] ?? null, self::SUPPORTED_FORMATS, true);
    }

    /**
     * @return array
     *
     * @throws UnsupportedFileExtensionException
     */
    public function readByRaws(string $path): iterable
    {
        if ($this->isSupportedFile($path)) {
            $fileHandle = fopen($path, 'r');

            while (!feof($fileHandle)) {
                yield fgetcsv($fileHandle);
            }

            fclose($fileHandle);
        } else {
            throw new UnsupportedFileExtensionException();
        }
    }
}
