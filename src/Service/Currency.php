<?php

namespace CommissionTask\Service;

class Currency
{
    public const EUR = 'EUR';
    public const USD = 'USD';
    public const JPY = 'JPY';

    /**
     * @return string[]
     */
    public static function getSupportedCurrencies(): array
    {
        return [
            self::EUR,
            self::USD,
            self::JPY,
        ];
    }

    public function isSupportedCurrency(string $currencyCode): bool
    {
        return  \in_array($currencyCode, self::getSupportedCurrencies());
    }
}