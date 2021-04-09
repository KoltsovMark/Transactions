<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use CommissionTask\Model\Customer as CustomerModel;

class Customer
{
    public static function isSupportedCustomerType(string $customerType): bool
    {
        return \in_array($customerType, [CustomerModel::LEGAL_TYPE, CustomerModel::NATURAL_TYPE], true);
    }
}
