<?php

declare(strict_types=1);

namespace CommissionTask\Factory\Commission;

use CommissionTask\Dto\Commission\CashOutLegalCommission as CashOutLegalCommissionDto;
use CommissionTask\Dto\Transaction\NewTransaction as NewTransactionDto;

class CashOutLegalCommission
{
    public function createEmpty(): CashOutLegalCommissionDto
    {
        return new CashOutLegalCommissionDto();
    }

    public function createFromNewTransactionDto(NewTransactionDto $newTransactionDto): CashOutLegalCommissionDto
    {
        $dto = $this->createEmpty();

        $dto->setAmount($newTransactionDto->getAmount())
            ->setCurrencyCode($newTransactionDto->getCurrencyCode())
        ;

        return $dto;
    }
}
