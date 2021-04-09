<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use CommissionTask\Dto\TransactionCommission as TransactionCommissionDto;
use CommissionTask\Exception\FunctionalInDevelopment as FunctionalInDevelopmentException;
use CommissionTask\Service\Currency as CurrencyService;
use CommissionTask\Service\Transaction as TransactionService;

class Commission
{
    public const TYPE_PERCENTAGE = 1;
    public const TYPE_VALUE = 2;

    public const TYPE_RENEWAL_WEEKLY = 1;

    public const CONFIGURATION = [
        'cash_in' => [
            'fee' => [
                'value' => '0.03',
                'type' => self::TYPE_PERCENTAGE,
                'max_amount' => '5.00',
                'max_amount_type' => self::TYPE_VALUE,
                'max_amount_currency' => 'EUR',
            ],
        ],
        'cash_out' => [
            'natural_person' => [
                'fee' => [
                    'value' => '0.3',
                    'type' => self::TYPE_PERCENTAGE,
                ],
                'free_of_charge' => [
                    'limit' => '1000.00',
                    'currency' => 'EUR',
                    'max_transactions' => 3,
                    'renewal' => self::TYPE_RENEWAL_WEEKLY,
                    'allow_exceeded_amount_fee' => true,
                ],
            ],
            'legal_person' => [
                'fee' => [
                    'value' => '0.3',
                    'type' => self::TYPE_PERCENTAGE,
                    'min_amount' => '0.50',
                    'min_amount_type' => self::TYPE_VALUE,
                    'min_amount_currency' => 'EUR',
                ],
            ],
        ],
    ];

    private CurrencyService $currencyService;
    private TransactionService $transactionService;

    /**
     * Commission constructor.
     *
     * @param Currency    $currencyService
     * @param Transaction $transactionService
     */
    public function __construct(CurrencyService $currencyService, TransactionService $transactionService)
    {
        $this->currencyService = $currencyService;
        $this->transactionService = $transactionService;
    }

    /**
     * @throws FunctionalInDevelopmentException
     */
    public function calculateCashInCommission(TransactionCommissionDto $transactionCommissionDto): string
    {
        // @todo add validation

        if (self::CONFIGURATION['cash_in']['fee']['type'] === self::TYPE_PERCENTAGE) {
            $fee = $this->calculateFeePercentage(
                $transactionCommissionDto->getAmount(),
                self::CONFIGURATION['cash_in']['fee']['value'],
                $transactionCommissionDto->getCurrencyCode()
            );

            return $this->applyCashInMaxLimitCheck($fee, $transactionCommissionDto->getCurrencyCode());
        }

        throw new FunctionalInDevelopmentException();
    }

    public function calculateCashOutNaturalCommission(TransactionCommissionDto $transactionCommissionDto): string
    {
        // @todo add validation

        return $this->applyCashOutNaturalFreeOfChargeCheck(
            $transactionCommissionDto->getAmount(),
            $transactionCommissionDto->getCurrencyCode(),
            $transactionCommissionDto->getCreatedAt(),
            $transactionCommissionDto->getCustomerId()
        );
    }

    /**
     * @throws FunctionalInDevelopmentException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function calculateCashOutLegalCommission(TransactionCommissionDto $transactionCommissionDto): string
    {
        // @todo add validation

        if (self::CONFIGURATION['cash_out']['legal_person']['fee']['type'] === self::TYPE_PERCENTAGE) {
            $fee = $this->calculateFeePercentage(
                $transactionCommissionDto->getAmount(),
                self::CONFIGURATION['cash_out']['legal_person']['fee']['value'],
                $transactionCommissionDto->getCurrencyCode()
            );

            return $this->applyCashOutLegalMinLimitCheck($fee, $transactionCommissionDto->getCurrencyCode());
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
     * @throws \CommissionTask\Exception\RateDoNotExist
     */
    protected function applyCashInMaxLimitCheck(string $amount, string $currencyCode): string
    {
        $fee = $amount;

        if (self::CONFIGURATION['cash_in']['fee']['max_amount_type'] === self::TYPE_VALUE) {
            $isExceedsCashInMaxLimit = $this->currencyService->isGreaterThan(
                $amount,
                $currencyCode,
                self::CONFIGURATION['cash_in']['fee']['max_amount'],
                self::CONFIGURATION['cash_in']['fee']['max_amount_currency']
            );

            if ($isExceedsCashInMaxLimit) {
                $fee = $this->currencyService->convertCurrency(
                    self::CONFIGURATION['cash_in']['fee']['max_amount'],
                    $currencyCode,
                    self::CONFIGURATION['cash_in']['fee']['max_amount_currency']
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
     * @throws \CommissionTask\Exception\RateDoNotExist
     */
    protected function applyCashOutLegalMinLimitCheck(string $amount, string $currencyCode): string
    {
        $fee = $amount;

        if (self::CONFIGURATION['cash_out']['legal_person']['fee']['min_amount_type'] === self::TYPE_VALUE) {
            $isExceedsCashInMinLimit = $this->currencyService->isGreaterThan(
                $amount,
                $currencyCode,
                self::CONFIGURATION['cash_out']['legal_person']['fee']['min_amount'],
                self::CONFIGURATION['cash_out']['legal_person']['fee']['min_amount_currency']
            );

            if (!$isExceedsCashInMinLimit) {
                $fee = $this->currencyService->convertCurrency(
                    self::CONFIGURATION['cash_out']['legal_person']['fee']['min_amount'],
                    $currencyCode,
                    self::CONFIGURATION['cash_out']['legal_person']['fee']['min_amount_currency']
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
     * @throws \CommissionTask\Exception\RateDoNotExist
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
                self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency'],
            );
        }

        if (self::CONFIGURATION['cash_out']['natural_person']['fee']['type'] === self::TYPE_PERCENTAGE) {
            $ifFeePositive = $this->currencyService->isPositive(
                $feeAmount,
                $transactionCurrency
            );

            if ($ifFeePositive) {
                return $this->calculateFeePercentage(
                    $feeAmount,
                    self::CONFIGURATION['cash_out']['natural_person']['fee']['value'],
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
            self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['renewal']
            === self::TYPE_RENEWAL_WEEKLY
        ) {
            $transactions = $this->transactionService->getWeeklyCashOutTransactionsByCustomerAndDate(
                $customerId,
                $transactionDate
            );
            $maxTransactions = self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['max_transactions'];

            return count($transactions) >= $maxTransactions;
        } else {
            throw new FunctionalInDevelopmentException();
        }
    }

    /**
     * @throws FunctionalInDevelopmentException
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \CommissionTask\Exception\RateDoNotExist
     */
    protected function isExceedCashOutNaturalFreeOfChargeTransactionsAmountLimit(
        int $customerId,
        string $transactionDate
    ): bool {
        if (
            self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['renewal']
            === self::TYPE_RENEWAL_WEEKLY
        ) {
            $transactions = $this->transactionService->getWeeklyCashOutTransactionsByCustomerAndDate(
                $customerId,
                $transactionDate
            );
            $transactionsAmount = $this->currencyService->getEmptyAmount(
                self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency']
            );

            if (!empty($transactions)) {
                foreach ($transactions as $transaction) {
                    $transactionsAmount = $this->currencyService->add(
                        $transactionsAmount,
                        self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency'],
                        $transaction->getAmount(),
                        $transaction->getCurrency()->getCode()
                    );
                }
            }

            return $this->currencyService->isGreaterThanOrEqual(
                $transactionsAmount,
                self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency'],
                self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['limit'],
                self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency'],
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
     * @throws \CommissionTask\Exception\RateDoNotExist
     */
    protected function calculateCashOutNaturalFreeOfChargeAmountReminderInBaseCurrency(
        int $customerId,
        string $transactionDate
    ): string {
        if (
            self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['renewal']
            === self::TYPE_RENEWAL_WEEKLY
        ) {
            $transactionsAmountInBaseCurrency = $this->currencyService->getEmptyAmount(
                self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency']
            );

            $transactions = $this->transactionService->getWeeklyCashOutTransactionsByCustomerAndDate(
                $customerId,
                $transactionDate
            );

            if (!empty($transactions)) {
                foreach ($transactions as $transaction) {
                    $transactionsAmountInBaseCurrency = $this->currencyService->add(
                        $transactionsAmountInBaseCurrency,
                        self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency'],
                        $transaction->getAmount(),
                        $transaction->getCurrency()->getCode()
                    );
                }
            }

            $availableDiscount = $this->currencyService->minus(
                self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['limit'],
                self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency'],
                $transactionsAmountInBaseCurrency,
                self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency'],
            );

            $isDiscountPositive = $this->currencyService->isPositive(
                $availableDiscount,
                self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency']
            );

            if ($isDiscountPositive) {
                return $availableDiscount;
            } else {
                return $this->currencyService->getEmptyAmount(
                    self::CONFIGURATION['cash_out']['natural_person']['free_of_charge']['currency']
                );
            }
        } else {
            throw new FunctionalInDevelopmentException();
        }
    }
}
