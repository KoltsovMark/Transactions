<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use CommissionTask\Model\Currency\Currency as CurrencyModel;
use CommissionTask\Service\Commission\Commission as CommissionService;

class Configuration
{
    public const DELIMITER = '.';

    private array $configuration = [
        'currencies' => [
            CurrencyModel::EUR,
            CurrencyModel::USD,
            CurrencyModel::JPY,
        ],
        'rates' => [
            CurrencyModel::EUR => [
                CurrencyModel::USD => '1.1497',
                CurrencyModel::JPY => '129.53',
            ],
        ],
        'commission' => [
            'cash_in' => [
                'fee' => [
                    'value' => '0.03',
                    'type' => CommissionService::COMMISSION_TYPE_PERCENTAGE,
                    'max_amount' => '5.00',
                    'max_amount_type' => CommissionService::COMMISSION_TYPE_VALUE,
                    'max_amount_currency' => CurrencyModel::EUR,
                ],
            ],
            'cash_out' => [
                'natural_person' => [
                    'fee' => [
                        'value' => '0.3',
                        'type' => CommissionService::COMMISSION_TYPE_PERCENTAGE,
                    ],
                    'free_of_charge' => [
                        'limit' => '1000.00',
                        'currency' => CurrencyModel::EUR,
                        'max_transactions' => 3,
                        'renewal' => CommissionService::COMMISSION_TYPE_RENEWAL_WEEKLY,
                        'allow_exceeded_amount_fee' => true,
                    ],
                ],
                'legal_person' => [
                    'fee' => [
                        'value' => '0.3',
                        'type' => CommissionService::COMMISSION_TYPE_PERCENTAGE,
                        'min_amount' => '0.50',
                        'min_amount_type' => CommissionService::COMMISSION_TYPE_VALUE,
                        'min_amount_currency' => CurrencyModel::EUR,
                    ],
                ],
            ],
        ],
    ];

    public function get(string $path)
    {
        return $this->read($path, $this->configuration);
    }

    private function read(string $path, array $data)
    {
        $pathArray = \explode(self::DELIMITER, $path);

        if (\count($pathArray) > 1) {
            $key = \array_shift($pathArray);

            return $this->read(\implode(self::DELIMITER, $pathArray), $data[$key]);
        } else {
            return $data[$path] ?? null;
        }
    }
}
