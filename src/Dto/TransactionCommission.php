<?php

declare(strict_types=1);

namespace CommissionTask\Dto;

class TransactionCommission
{
    protected string $amount;
    protected string $currencyCode;
    protected string $customerId;
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
     * @return TransactionCommission
     */
    public function setAmount(string $amount): TransactionCommission
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
     * @return TransactionCommission
     */
    public function setCurrencyCode(string $currencyCode): TransactionCommission
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    /**
     * @param string $customerId
     *
     * @return TransactionCommission
     */
    public function setCustomerId(string $customerId): TransactionCommission
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
     * @return TransactionCommission
     */
    public function setCustomerType(string $customerType): TransactionCommission
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
     * @return TransactionCommission
     */
    public function setTransactionType(string $transactionType): TransactionCommission
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
     * @return TransactionCommission
     */
    public function setCreatedAt(string $createdAt): TransactionCommission
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}