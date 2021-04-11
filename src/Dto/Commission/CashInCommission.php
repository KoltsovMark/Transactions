<?php

declare(strict_types=1);

namespace CommissionTask\Dto\Commission;

class CashInCommission
{
    private string $amount;
    private string $currencyCode;

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): CashInCommission
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): CashInCommission
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }
}
