<?php

declare(strict_types=1);

namespace CommissionTask\Repository\Rate;

use CommissionTask\Contract\Repository as RepositoryInterface;
use CommissionTask\Model\Rate\Rate as RateModel;

class Rate implements RepositoryInterface
{
    protected static $instance;

    /**
     * @var RateModel[]
     */
    private array $rates = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return RateModel[]
     */
    public function getALl(): array
    {
        return $this->rates;
    }

    public function getRateByCodesOrNull(string $baseCurrency, string $quoteCurrency): ?RateModel
    {
        foreach ($this->getALl() as $rate) {
            if ($rate->getBaseCurrency() === $baseCurrency && $rate->getQuoteCurrency() === $quoteCurrency) {
                return $rate;
            }
        }

        return null;
    }

    /**
     * @return $this
     */
    public function add(RateModel $rate): Rate
    {
        if (is_null($this->getRateByCodesOrNull($rate->getBaseCurrency(), $rate->getQuoteCurrency()))) {
            $this->rates[] = $rate;
        }

        return $this;
    }
}
