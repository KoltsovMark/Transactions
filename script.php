<?php

require __DIR__ . '/vendor/autoload.php';

use CommissionTask\Exception\Transaction\SourceOfTransactionsDoNotExist as SourceOfTransactionsDoNotExistException;
use CommissionTask\Exception\Transaction\SourceOfTransactionsDoNotMatchFormat as SourceOfTransactionsDoNotMatchFormatException;
use CommissionTask\Factory\Commission\CashInCommission as CashInCommissionFactory;
use CommissionTask\Factory\Commission\CashOutLegalCommission as CashOutLegalCommissionFactory;
use CommissionTask\Factory\Commission\CashOutNaturalCommission as CashOutNaturalCommissionFactory;
use CommissionTask\Factory\Currency\Currency as CurrencyFactory;
use CommissionTask\Factory\Customer\Customer as CustomerFactory;
use CommissionTask\Factory\Rate\Rate as RateFactory;
use CommissionTask\Factory\Transaction\NewTransaction as NewTransactionFactory;
use CommissionTask\Factory\Transaction\Transaction as TransactionFactory;
use CommissionTask\Repository\Currency\Currency as CurrencyRepository;
use CommissionTask\Repository\Rate\Rate as RateRepository;
use CommissionTask\Repository\Transaction\Transaction as TransactionRepository;
use CommissionTask\Service\Commission\Commission as CommissionService;
use CommissionTask\Service\Currency\Currency as CurrencyService;
use CommissionTask\Service\Math as MathService;
use CommissionTask\Service\Rate\Rate as RateService;
use CommissionTask\Service\Transaction\Transaction as TransactionService;
use CommissionTask\Service\Transaction\TransactionOperation as TransactionOperationService;
use CommissionTask\Validator\Transaction\ProcessTransaction as ProcessTransactionValidator;

//@todo add DI autowire
// Init Repositories
$currencyRepository = CurrencyRepository::getInstance();
$rateRepository = RateRepository::getInstance();
$transactionRepository = TransactionRepository::getInstance();

// Init Factories
$newTransactionFactory = new NewTransactionFactory();
$transactionFactory = new TransactionFactory();
$customerFactory = new CustomerFactory();
$currencyFactory = new CurrencyFactory();
$rateFactory = new RateFactory();
$cashInCommissionFactory = new CashInCommissionFactory();
$cashOutLegalCommissionFactory = new CashOutLegalCommissionFactory();
$cashOutNaturalCommissionFactory = new CashOutNaturalCommissionFactory();

// Init Services
$mathService = new MathService();
$transactionService = new TransactionService($transactionRepository);
$rateService = new RateService(new MathService(8), $rateRepository, $rateFactory);
$currencyService = new CurrencyService($rateService, $currencyRepository, $currencyFactory);
$commissionService = new CommissionService($currencyService, $transactionService);
$transactionOperationService = new TransactionOperationService(
    $commissionService,
    $transactionRepository,
    $transactionFactory,
    $customerFactory,
    $currencyFactory,
    $cashInCommissionFactory,
    $cashOutLegalCommissionFactory,
    $cashOutNaturalCommissionFactory
);

//Init Validators
$processTransactionValidator = new ProcessTransactionValidator();

$transactionsSource = null;

if (isset($argc) && $argc === 2) {
    $transactionsSource = __DIR__ . "/{$argv[1]}";
}

if (file_exists($transactionsSource)) {
    $fileHandle = fopen($transactionsSource, 'r');

    while ( ! feof($fileHandle)) {
        $raw = fgetcsv($fileHandle);

        if ($raw) {
            $newTransactionDto = $newTransactionFactory->createFromArray($raw);

            if ($processTransactionValidator->isValid($newTransactionDto->toArray())) {
                $transactionOperationService->processTransaction($newTransactionDto);
            } else {
                throw new SourceOfTransactionsDoNotMatchFormatException($processTransactionValidator->getFirstError());
            }
        }
    }

    fclose($fileHandle);

    foreach ($transactionRepository->getAll() as $transaction) {
        print_r($transaction->getCommission() . PHP_EOL);
    }
} else {
    throw new SourceOfTransactionsDoNotExistException();
}
