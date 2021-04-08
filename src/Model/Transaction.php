<?php

namespace CommissionTask\Model;

use DateTime;

class Transaction
{
    public const TYPE_CASH_IN = 'cash_in';
    public const TYPE_CASH_OUT = 'cash_out';

    private string $amount;
    private string $commission;
    private Currency $currency;
    private Customer $customer;
    private string $type;
    private DateTime $createdAt;
    private Rate $rate;

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
     * @return Transaction
     */
    public function setAmount(string $amount): Transaction
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommission(): string
    {
        return $this->commission;
    }

    /**
     * @param string $commission
     *
     * @return Transaction
     */
    public function setCommission(string $commission): Transaction
    {
        $this->commission = $commission;
        return $this;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     *
     * @return Transaction
     */
    public function setCurrency(Currency $currency): Transaction
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     *
     * @return Transaction
     */
    public function setCustomer(Customer $customer): Transaction
    {
        $this->customer = $customer;
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
     * @return Transaction
     */
    public function setType(string $type): Transaction
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     *
     * @return Transaction
     */
    public function setCreatedAt(DateTime $createdAt): Transaction
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Rate
     */
    public function getRate(): Rate
    {
        return $this->rate;
    }

    /**
     * @param Rate $rate
     *
     * @return Transaction
     */
    public function setRate(Rate $rate): Transaction
    {
        $this->rate = $rate;
        return $this;
    }

    public function isCashIn(): bool
    {
        return $this->getType() === self::TYPE_CASH_IN;
    }

    public function isCashOut(): bool
    {
        return $this->getType() === self::TYPE_CASH_OUT;
    }
}