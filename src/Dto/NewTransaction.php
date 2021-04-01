<?php

declare(strict_types=1);

namespace CommissionTask\Dto;

class NewTransaction
{
    protected string $amount;
    protected string $currencyCode;
    protected int $customerId;
    protected string $customerType;
    protected string $transactionType;
    protected string $createdAt;

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     *
     * @return NewTransaction
     */
    public function setAmount(string $amount): NewTransaction
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     *
     * @return NewTransaction
     */
    public function setCurrencyCode(string $currencyCode): NewTransaction
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     *
     * @return NewTransaction
     */
    public function setCustomerId(int $customerId): NewTransaction
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerType(): string
    {
        return $this->customerType;
    }

    /**
     * @param string $customerType
     *
     * @return NewTransaction
     */
    public function setCustomerType(string $customerType): NewTransaction
    {
        $this->customerType = $customerType;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionType(): string
    {
        return $this->TransactionType;
    }

    /**
     * @param string $transactionType
     *
     * @return NewTransaction
     */
    public function setTransactionType(string $transactionType): NewTransaction
    {
        $this->TransactionType = $transactionType;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     *
     * @return NewTransaction
     */
    public function setCreatedAt(string $createdAt): NewTransaction
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}