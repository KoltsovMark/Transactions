<?php

declare(strict_types=1);

namespace CommissionTask\Contract;

interface DataValidator
{
    public function isValid(array $data): bool;

    public function getErrors(): array;

    public function setError(string $key, string $message);

    public function getFirstError(): ?string;
}
