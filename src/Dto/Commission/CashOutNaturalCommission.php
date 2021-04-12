<?php

declare(strict_types=1);

namespace CommissionTask\Dto\Commission;

class CashOutNaturalCommission
{
    private string $amount;
    private string $currencyCode;
    private int $customerId;
    private string $createdAt;

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): CashOutNaturalCommission
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): CashOutNaturalCommission
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): CashOutNaturalCommission
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): CashOutNaturalCommission
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
