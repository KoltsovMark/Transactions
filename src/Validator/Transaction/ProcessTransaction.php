<?php

declare(strict_types=1);

namespace CommissionTask\Validator\Transaction;

use Carbon\Carbon;
use CommissionTask\Service\Currency\Currency as CurrencyService;
use CommissionTask\Service\Customer\Customer as CustomerService;
use CommissionTask\Service\Transaction\Transaction as TransactionService;
use CommissionTask\Validator\AbstractValidator;
use Exception;

class ProcessTransaction extends AbstractValidator
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function isValid(array $data): bool
    {
        return $this->isValidDate($data['createdAt'] ?? null)
            && $this->isValidCustimerId($data['customerId'] ?? null)
            && $this->isValidCustomerType($data['customerType'] ?? null)
            && $this->isValidTransactionType($data['transactionType'] ?? null)
            && $this->isValidAmount($data['amount'] ?? null)
            && $this->isValidCurrencyCode($data['currencyCode'] ?? null)
        ;
    }

    protected function isValidDate($value): bool
    {
        try {
            $date = new Carbon($value);
        } catch (Exception $exception) {
            $date = null;
        }

        $isValid = is_string($value) && $date;

        if (!$isValid) {
            $this->setError(__FUNCTION__);
        }

        return $isValid;
    }

    protected function isValidCustimerId($value): bool
    {
        $isValid = is_int($value);

        if (!$isValid) {
            $this->setError(__FUNCTION__);
        }

        return $isValid;
    }

    protected function isValidCustomerType($value): bool
    {
        $isValid = is_string($value) && CustomerService::isSupportedCustomerType($value);

        if (!$isValid) {
            $this->setError(__FUNCTION__);
        }

        return $isValid;
    }

    protected function isValidTransactionType($value): bool
    {
        $isValid = is_string($value) && TransactionService::isSupportedTransactionType($value);

        if (!$isValid) {
            $this->setError(__FUNCTION__);
        }

        return $isValid;
    }

    protected function isValidAmount($value): bool
    {
        $isValid = is_string($value) && is_numeric($value);

        if (!$isValid) {
            $this->setError(__FUNCTION__);
        }

        return $isValid;
    }

    protected function isValidCurrencyCode($value): bool
    {
        $isValid = is_string($value) && $this->currencyService->isSupportedCurrencyCode($value);

        if (!$isValid) {
            $this->setError(__FUNCTION__);
        }

        return $isValid;
    }
}
