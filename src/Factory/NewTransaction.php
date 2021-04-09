<?php

declare(strict_types=1);

namespace CommissionTask\Factory;

use CommissionTask\Dto\NewTransaction as NewTransactionDto;

class NewTransaction
{
    public function createEmpty(): NewTransactionDto
    {
        return new NewTransactionDto();
    }

    public function createFromArray(array $data): NewTransactionDto
    {
        $dto = $this->createEmpty();

        $dto->setCreatedAt($data[0])
            ->setCustomerId((int) $data[1])
            ->setCustomerType($data[2])
            ->setTransactionType($data[3])
            ->setAmount($data[4])
            ->setCurrencyCode($data[5])
        ;

        return $dto;
    }
}
