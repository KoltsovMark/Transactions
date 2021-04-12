<?php

declare(strict_types=1);

namespace CommissionTask\Factory\Customer;

use CommissionTask\Dto\Transaction\NewTransaction as NewTransactionDto;
use CommissionTask\Model\Customer\Customer as CustomerModel;

class Customer
{
    public function createEmpty(): CustomerModel
    {
        return new CustomerModel();
    }

    public function createFromNewTransactionDto(NewTransactionDto $newTransactionDto): CustomerModel
    {
        $customer = $this->createEmpty();

        $customer->setId($newTransactionDto->getCustomerId())
            ->setType($newTransactionDto->getCustomerType())
        ;

        return $customer;
    }
}
