<?php

declare(strict_types=1);

namespace CommissionTask\Factory\Commission;

use CommissionTask\Dto\Commission\CashInCommission as CashInCommissionDto;
use CommissionTask\Dto\Transaction\NewTransaction as NewTransactionDto;

class CashInCommission
{
    public function createEmpty(): CashInCommissionDto
    {
        return new CashInCommissionDto();
    }

    public function createFromNewTransactionDto(NewTransactionDto $newTransactionDto): CashInCommissionDto
    {
        $dto = $this->createEmpty();

        $dto->setAmount($newTransactionDto->getAmount())
            ->setCurrencyCode($newTransactionDto->getCurrencyCode())
        ;

        return $dto;
    }
}
