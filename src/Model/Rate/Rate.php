<?php

declare(strict_types=1);

namespace CommissionTask\Model\Rate;

class Rate
{
    private string $baseCurrency;
    private string $quoteCurrency;
    private string $rate;

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(string $baseCurrency): Rate
    {
        $this->baseCurrency = $baseCurrency;

        return $this;
    }

    public function getQuoteCurrency(): string
    {
        return $this->quoteCurrency;
    }

    public function setQuoteCurrency(string $quoteCurrency): Rate
    {
        $this->quoteCurrency = $quoteCurrency;

        return $this;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function setRate(string $rate): Rate
    {
        $this->rate = $rate;

        return $this;
    }
}
