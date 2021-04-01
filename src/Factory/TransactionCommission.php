<?php

namespace CommissionTask\Factory;

use CommissionTask\Dto\TransactionCommission as TransactionCommissionDto;
use CommissionTask\Dto\NewTransaction as NewTransactionDto;

class TransactionCommission
{
    /**
     * @return TransactionCommissionDto
     */
    public function createEmpty(): TransactionCommissionDto
    {
        return new TransactionCommissionDto();
    }

    /**
     * @param NewTransactionDto $newTransactionDto
     *
     * @return TransactionCommissionDto
     */
    public function createFromNewTransactionDto(NewTransactionDto $newTransactionDto): TransactionCommissionDto
    {
        $dto = $this->createEmpty();

        $dto->setCreatedAt($newTransactionDto->getCreatedAt())
            ->setCustomerId($newTransactionDto->getCustomerId())
            ->setCustomerType($newTransactionDto->getCustomerType())
            ->setTransactionType($newTransactionDto->getTransactionType())
            ->setAmount($newTransactionDto->getAmount())
            ->setCurrencyCode($newTransactionDto->getCurrencyCode())
        ;

        return $dto;
    }
}