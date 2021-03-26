<?php

namespace CommissionTask\Service;

class Rate
{
    protected const DEFAULT_RATES = [
      'EUR' => [
          'USD' => '1.1497',
          'JPY' => '129.53'
      ]
    ];

    public static function getDefaultRatesArray(): array
    {
        return self::DEFAULT_RATES;
    }
}