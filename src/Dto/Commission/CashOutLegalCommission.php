<?php

declare(strict_types=1);

namespace CommissionTask\Dto\Commission;

class CashOutLegalCommission
{
    private string $amount;
    private string $currencyCode;

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): CashOutLegalCommission
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): CashOutLegalCommission
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }
}
