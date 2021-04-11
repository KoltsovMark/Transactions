<?php

declare(strict_types=1);

namespace CommissionTask\Factory\Commission;

use CommissionTask\Dto\Commission\TransactionCommission as TransactionCommissionDto;
use CommissionTask\Dto\Transaction\NewTransaction as NewTransactionDto;

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
