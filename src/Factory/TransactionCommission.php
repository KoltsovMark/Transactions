<?php

declare(strict_types=1);

namespace CommissionTask\Factory;

use CommissionTask\Dto\NewTransaction as NewTransactionDto;
use CommissionTask\Dto\TransactionCommission as TransactionCommissionDto;

class TransactionCommission
{
    public function createEmpty(): TransactionCommissionDto
    {
        return new TransactionCommissionDto();
    }

    public function createFromNewTransactionDto(NewTransactionDto $newTransactionDto): TransactionCommissionDto
    {
        $dto = $this->createEmpty();

        $dto->setCreatedAt($newTransactionDto->getCreatedAt())
            ->setCustomerId((int) $newTransactionDto->getCustomerId())
            ->setCustomerType($newTransactionDto->getCustomerType())
            ->setTransactionType($newTransactionDto->getTransactionType())
            ->setAmount($newTransactionDto->getAmount())
            ->setCurrencyCode($newTransactionDto->getCurrencyCode())
        ;

        return $dto;
    }
}
