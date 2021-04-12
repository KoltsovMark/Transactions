<?php

declare(strict_types=1);

namespace CommissionTask\Service\Commission;

use CommissionTask\Dto\Commission\CashInCommission as CashInCommissionDto;
use CommissionTask\Dto\Commission\CashOutLegalCommission as CashOutLegalCommissionDto;
use CommissionTask\Dto\Commission\CashOutNaturalCommission as CashOutNaturalCommissionDto;
use CommissionTask\Exception\FunctionalInDevelopment as FunctionalInDevelopmentException;
use CommissionTask\Service\Configuration as ConfigurationService;
use CommissionTask\Service\Currency\Currency as CurrencyService;
use CommissionTask\Service\Transaction\Transaction as TransactionService;

class Commission
{
    public const COMMISSION_TYPE_PERCENTAGE = 1;
    public const COMMISSION_TYPE_VALUE = 2;

    public const COMMISSION_TYPE_RENEWAL_WEEKLY = 1;

    private ConfigurationService $configurationService;
    private CurrencyService $currencyService;
    private TransactionService $transactionService;

    /**
     * Commission constructor.
     */
    public function __construct(
        ConfigurationService $configurationService,
        CurrencyService $currencyService,
        TransactionService $transactionService
    ) {
        $this->configurationService = $configurationService;
        $this->currencyService = $currencyService;
        $this->transactionService = $transactionService;
    }

    /**
     * @throws FunctionalInDevelopmentException
     */
    public function calculateCashInCommission(CashInCommissionDto $cashInCommissionDto): string
    {
        if ($this->configurationService->get('commission.cash_in.fee.type') === self::COMMISSION_TYPE_PERCENTAGE) {
            $fee = $this->calculateFeePercentage(
                $cashInCommissionDto->getAmount(),
                $this->configurationService->get('commission.cash_in.fee.value'),
                $cashInCommissionDto->getCurrencyCode()
            );

            return $this->applyCashInMaxLimitCheck($fee, $cashInCommissionDto->getCurrencyCode());
        }

        throw new FunctionalInDevelopmentException();
    }

    public function calculateCashOutNaturalCommission(CashOutNaturalCommissionDto $cashOutNaturalCommissionDto): string
    {
        return $this->applyCashOutNaturalFreeOfChargeCheck(
            $cashOutNaturalCommissionDto->getAmount(),
            $cashOutNaturalCommissionDto->getCurrencyCode(),
            $cashOutNaturalCommissionDto->getCreatedAt(),
            $cashOutNaturalCommissionDto->getCustomerId()
        );
    }

    /**
     * @throws FunctionalInDevelopmentException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function calculateCashOutLegalCommission(CashOutLegalCommissionDto $cashOutLegalCommissionDto): string
    {
        if (
            $this->configurationService->get('commission.cash_out.legal_person.fee.type')
            === self::COMMISSION_TYPE_PERCENTAGE
        ) {
            $fee = $this->calculateFeePercentage(
                $cashOutLegalCommissionDto->getAmount(),
                $this->configurationService->get('commission.cash_out.legal_person.fee.value'),
                $cashOutLegalCommissionDto->getCurrencyCode()
            );

            return $this->applyCashOutLegalMinLimitCheck($fee, $cashOutLegalCommissionDto->getCurrencyCode());
        }

        throw new FunctionalInDevelopmentException();
    }

    /**
     * @return string
     *
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    protected function calculateFeePercentage(string $amount, string $feePercentage, string $currencyCode)
    {
        return $this->currencyService->getFeePercentageForCurrency(
            $amount,
            $feePercentage,
            $currencyCode
        );
    }

    /**
     * @throws FunctionalInDevelopmentException
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\Rate\RateDoNotExist
     */
    protected function applyCashInMaxLimitCheck(string $amount, string $currencyCode): string
    {
        $fee = $amount;

        if ($this->configurationService->get('commission.cash_in.fee.max_amount_type') === self::COMMISSION_TYPE_VALUE) {
            $isExceedsCashInMaxLimit = $this->currencyService->isGreaterThan(
                $amount,
                $currencyCode,
                $this->configurationService->get('commission.cash_in.fee.max_amount'),
                $this->configurationService->get('commission.cash_in.fee.max_amount_currency')
            );

            if ($isExceedsCashInMaxLimit) {
                $fee = $this->currencyService->convertCurrency(
                    $this->configurationService->get('commission.cash_in.fee.max_amount'),
                    $currencyCode,
                    $this->configurationService->get('commission.cash_in.fee.max_amount_currency')
                );
            }

            return $fee;
        }

        throw new FunctionalInDevelopmentException();
    }

    /**
     * @throws FunctionalInDevelopmentException
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\Rate\RateDoNotExist
     */
    protected function applyCashOutLegalMinLimitCheck(string $amount, string $currencyCode): string
    {
        $fee = $amount;

        if (
            $this->configurationService->get('commission.cash_out.legal_person.fee.min_amount_type')
            === self::COMMISSION_TYPE_VALUE
        ) {
            $isExceedsCashInMinLimit = $this->currencyService->isGreaterThan(
                $amount,
                $currencyCode,
                $this->configurationService->get('commission.cash_out.legal_person.fee.min_amount'),
                $this->configurationService->get('commission.cash_out.legal_person.fee.min_amount_currency')
            );

            if (!$isExceedsCashInMinLimit) {
                $fee = $this->currencyService->convertCurrency(
                    $this->configurationService->get('commission.cash_out.legal_person.fee.min_amount'),
                    $currencyCode,
                    $this->configurationService->get('commission.cash_out.legal_person.fee.min_amount_currency')
                );
            }

            return $fee;
        }

        throw new FunctionalInDevelopmentException();
    }

    /**
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\Rate\RateDoNotExist
     */
    protected function applyCashOutNaturalFreeOfChargeCheck(
        string $transactionAmount,
        string $transactionCurrency,
        string $transactionDate,
        int $customerId
    ): string {
        if (
            $this->isExceedCashOutNaturalFreeOfChargeTransactionsLimit($customerId, $transactionDate)
            || $this->isExceedCashOutNaturalFreeOfChargeTransactionsAmountLimit(
                $customerId,
                $transactionDate
            )
        ) {
            $feeAmount = $transactionAmount;
        } else {
            $availableDiscount = $this->calculateCashOutNaturalFreeOfChargeAmountReminderInBaseCurrency(
                $customerId,
                $transactionDate
            );

            $feeAmount = $this->currencyService->minus(
                $transactionAmount,
                $transactionCurrency,
                $availableDiscount,
                $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency')
            );
        }

        if (
            $this->configurationService->get('commission.cash_out.natural_person.fee.type')
            === self::COMMISSION_TYPE_PERCENTAGE
        ) {
            $ifFeePositive = $this->currencyService->isPositive(
                $feeAmount,
                $transactionCurrency
            );

            if ($ifFeePositive) {
                return $this->calculateFeePercentage(
                    $feeAmount,
                    $this->configurationService->get('commission.cash_out.natural_person.fee.value'),
                    $transactionCurrency
                );
            } else {
                return $this->currencyService->getEmptyAmount($transactionCurrency);
            }
        } else {
            throw new FunctionalInDevelopmentException();
        }
    }

    /**
     * @throws FunctionalInDevelopmentException
     */
    protected function isExceedCashOutNaturalFreeOfChargeTransactionsLimit(
        int $customerId,
        string $transactionDate
    ): bool {
        if (
            $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.renewal')
            === self::COMMISSION_TYPE_RENEWAL_WEEKLY
        ) {
            $transactions = $this->transactionService->getWeeklyCashOutTransactionsByCustomerAndDate(
                $customerId,
                $transactionDate
            );

            return count($transactions)
                >= $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.max_transactions');
        } else {
            throw new FunctionalInDevelopmentException();
        }
    }

    /**
     * @throws FunctionalInDevelopmentException
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\Rate\RateDoNotExist
     */
    protected function isExceedCashOutNaturalFreeOfChargeTransactionsAmountLimit(
        int $customerId,
        string $transactionDate
    ): bool {
        if (
            $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.renewal')
            === self::COMMISSION_TYPE_RENEWAL_WEEKLY
        ) {
            $transactions = $this->transactionService->getWeeklyCashOutTransactionsByCustomerAndDate(
                $customerId,
                $transactionDate
            );
            $transactionsAmount = $this->currencyService->getEmptyAmount(
                $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency')
            );

            if (!empty($transactions)) {
                foreach ($transactions as $transaction) {
                    $transactionsAmount = $this->currencyService->add(
                        $transactionsAmount,
                        $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency'),
                        $transaction->getAmount(),
                        $transaction->getCurrency()->getCode()
                    );
                }
            }

            return $this->currencyService->isGreaterThanOrEqual(
                $transactionsAmount,
                $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency'),
                $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.limit'),
                $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency'),
            );
        } else {
            throw new FunctionalInDevelopmentException();
        }
    }

    /**
     * @throws FunctionalInDevelopmentException
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\Rate\RateDoNotExist
     */
    protected function calculateCashOutNaturalFreeOfChargeAmountReminderInBaseCurrency(
        int $customerId,
        string $transactionDate
    ): string {
        if (
            $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.renewal')
            === self::COMMISSION_TYPE_RENEWAL_WEEKLY
        ) {
            $transactionsAmountInBaseCurrency = $this->currencyService->getEmptyAmount(
                $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency')
            );

            $transactions = $this->transactionService->getWeeklyCashOutTransactionsByCustomerAndDate(
                $customerId,
                $transactionDate
            );

            if (!empty($transactions)) {
                foreach ($transactions as $transaction) {
                    $transactionsAmountInBaseCurrency = $this->currencyService->add(
                        $transactionsAmountInBaseCurrency,
                        $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency'),
                        $transaction->getAmount(),
                        $transaction->getCurrency()->getCode()
                    );
                }
            }

            $availableDiscount = $this->currencyService->minus(
                $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.limit'),
                $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency'),
                $transactionsAmountInBaseCurrency,
                $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency'),
            );

            $isDiscountPositive = $this->currencyService->isPositive(
                $availableDiscount,
                $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency')
            );

            if ($isDiscountPositive) {
                return $availableDiscount;
            } else {
                return $this->currencyService->getEmptyAmount(
                    $this->configurationService->get('commission.cash_out.natural_person.free_of_charge.currency')
                );
            }
        } else {
            throw new FunctionalInDevelopmentException();
        }
    }
}
