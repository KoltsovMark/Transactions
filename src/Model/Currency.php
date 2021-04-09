<?php

declare(strict_types=1);

namespace CommissionTask\Model;

class Currency
{
    public const EUR = 'EUR';
    public const USD = 'USD';
    public const JPY = 'JPY';

    protected ?string $code = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return Currency
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
