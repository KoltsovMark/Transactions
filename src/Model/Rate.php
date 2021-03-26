<?php

namespace CommissionTask\Model;

class Rate
{
    protected string $baseCurrency;
    protected string $quoteCurrency;
    protected string $rate;

    public function __construct(
        string $baseCurrency,
        string $quoteCurrency,
        string $rate
    ) {
        $this->setBaseCurrency($baseCurrency)
            ->setQuoteCurrency($quoteCurrency)
            ->setRate($rate);
    }

    /**
     * @return string
     */
    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    /**
     * @param string $baseCurrency
     *
     * @return Rate
     */
    public function setBaseCurrency(string $baseCurrency): Rate
    {
        $this->baseCurrency = $baseCurrency;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuoteCurrency(): string
    {
        return $this->quoteCurrency;
    }

    /**
     * @param string $quoteCurrency
     *
     * @return Rate
     */
    public function setQuoteCurrency(string $quoteCurrency): Rate
    {
        $this->quoteCurrency = $quoteCurrency;
        return $this;
    }

    /**
     * @return string
     */
    public function getRate(): string
    {
        return $this->rate;
    }

    /**
     * @param string $rate
     *
     * @return Rate
     */
    public function setRate(string $rate): Rate
    {
        $this->rate = $rate;
        return $this;
    }
}