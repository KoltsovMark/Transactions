<?php

declare(strict_types=1);

namespace CommissionTask\Factory;

use CommissionTask\Dto\NewTransaction as NewTransactionDto;
use CommissionTask\Model\Customer as CustomerModel;

class Customer
{
    /**
     * @return CustomerModel
     */
    public function createEmpty(): CustomerModel
    {
        return new CustomerModel();
    }

    /**
     * @param NewTransactionDto $newTransactionDto
     *
     * @return CustomerModel
     */
    public function createFromNewTransactionDto(NewTransactionDto $newTransactionDto): CustomerModel
    {
        $customer = $this->createEmpty();

        $customer->setId($newTransactionDto->getCustomerId())
            ->setType($newTransactionDto->getCustomerType())
        ;

        return $customer;
    }
}