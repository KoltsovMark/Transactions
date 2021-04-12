<?php

declare(strict_types=1);

namespace CommissionTask\Model\Customer;

class Customer
{
    public const NATURAL_TYPE = 'natural';
    public const LEGAL_TYPE = 'legal';

    private int $id;
    private string $type;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Customer
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Customer
    {
        $this->type = $type;

        return $this;
    }

    public function isLegalPerson(): bool
    {
        return $this->getType() === self::LEGAL_TYPE;
    }

    public function isNaturalPerson(): bool
    {
        return $this->getType() === self::NATURAL_TYPE;
    }
}
