<?php

declare(strict_types=1);

namespace CommissionTask\Model\Transaction;

use CommissionTask\Model\Currency\Currency as CurrencyModel;
use CommissionTask\Model\Customer\Customer as CustomerModel;
use CommissionTask\Model\Rate\Rate as RateModel;
use DateTime;

class Transaction
{
    public const TYPE_CASH_IN = 'cash_in';
    public const TYPE_CASH_OUT = 'cash_out';

    private string $amount;
    private string $commission;
    private CurrencyModel $currency;
    private CustomerModel $customer;
    private string $type;
    private DateTime $createdAt;
    private RateModel $rate;

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): Transaction
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCommission(): string
    {
        return $this->commission;
    }

    public function setCommission(string $commission): Transaction
    {
        $this->commission = $commission;

        return $this;
    }

    public function getCurrency(): CurrencyModel
    {
        return $this->currency;
    }

    public function setCurrency(CurrencyModel $currency): Transaction
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCustomer(): CustomerModel
    {
        return $this->customer;
    }

    public function setCustomer(CustomerModel $customer): Transaction
    {
        $this->customer = $customer;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Transaction
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): Transaction
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRate(): RateModel
    {
        return $this->rate;
    }

    public function setRate(RateModel $rate): Transaction
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
