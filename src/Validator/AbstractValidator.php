<?php

declare(strict_types=1);

namespace CommissionTask\Validator;

use CommissionTask\Contract\DataValidator;

abstract class AbstractValidator implements DataValidator
{
    private const DEFAULT_MESSAGE = 'Provided value is not valid at %s';

    private array $errors = [];

    abstract public function isValid(array $data): bool;

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setError(string $key, string $message = null)
    {
        if (!$message) {
            $message = sprintf(self::DEFAULT_MESSAGE, $key);
        }

        $this->errors[$key] = $message;
    }

    public function getFirstError(): ?string
    {
        return $this->getErrors()[array_key_first($this->getErrors())] ?? null;
    }
}
