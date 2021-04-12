<?php

declare(strict_types=1);

namespace CommissionTask\Factory\Commission;

use CommissionTask\Dto\Commission\CashOutNaturalCommission as CashOutNaturalCommissionDto;
use CommissionTask\Dto\Transaction\NewTransaction as NewTransactionDto;

class CashOutNaturalCommission
{
    public function createEmpty(): CashOutNaturalCommissionDto
    {
        return new CashOutNaturalCommissionDto();
    }

    public function createFromNewTransactionDto(NewTransactionDto $newTransactionDto): CashOutNaturalCommissionDto
    {
        $dto = $this->createEmpty();

        $dto->setCreatedAt($newTransactionDto->getCreatedAt())
            ->setCustomerId((int) $newTransactionDto->getCustomerId())
            ->setAmount($newTransactionDto->getAmount())
            ->setCurrencyCode($newTransactionDto->getCurrencyCode())
        ;

        return $dto;
    }
}
