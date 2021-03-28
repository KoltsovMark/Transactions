<?php

namespace CommissionTask\Model;

class Customer
{
    public const NATURAL_TYPE = 'natural';
    public const LEGAL_TYPE = 'legal';

    protected int $id;
    protected string $type;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Customer
     */
    public function setId(int $id): Customer
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Customer
     */
    public function setType(string $type): Customer
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLegalPerson(): bool
    {
        return $this->getType() === self::NATURAL_TYPE;
    }

    /**
     * @return bool
     */
    public function isNaturalPerson(): bool
    {
        return $this->getType() === self::LEGAL_TYPE;
    }
}