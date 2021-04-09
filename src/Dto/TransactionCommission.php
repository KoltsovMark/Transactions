<?php

declare(strict_types=1);

namespace CommissionTask\Dto;

class TransactionCommission
{
    private string $amount;
    private string $currencyCode;
    private int $customerId;
    private string $customerType;
    private string $transactionType;
    private string $createdAt;

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): TransactionCommission
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): TransactionCommission
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): TransactionCommission
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCustomerType(): int
    {
        return $this->customerType;
    }

    public function setCustomerType(string $customerType): TransactionCommission
    {
        $this->customerType = $customerType;

        return $this;
    }

    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): TransactionCommission
    {
        $this->TransactionType = $transactionType;

        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): TransactionCommission
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
