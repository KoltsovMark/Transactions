<?php

declare(strict_types=1);

namespace CommissionTask\Service\Customer;

use CommissionTask\Model\Customer\Customer as CustomerModel;

class Customer
{
    public static function isSupportedCustomerType(string $customerType): bool
    {
        return \in_array($customerType, [CustomerModel::LEGAL_TYPE, CustomerModel::NATURAL_TYPE], true);
    }
}
