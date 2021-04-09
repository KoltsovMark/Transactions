<?php

declare(strict_types=1);

namespace CommissionTask\Dto;

use CommissionTask\Contract\ArrayAccess;

class NewTransaction implements ArrayAccess
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

    public function setAmount(string $amount): NewTransaction
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): NewTransaction
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): NewTransaction
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCustomerType(): string
    {
        return $this->customerType;
    }

    public function setCustomerType(string $customerType): NewTransaction
    {
        $this->customerType = $customerType;

        return $this;
    }

    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): NewTransaction
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): NewTransaction
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}
