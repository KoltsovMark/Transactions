<?php

declare(strict_types=1);

namespace CommissionTask\Model;

class Rate
{
    private string $baseCurrency;
    private string $quoteCurrency;
    private string $rate;

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