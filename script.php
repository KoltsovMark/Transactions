<?php

require __DIR__ . '/vendor/autoload.php';

use CommissionTask\Exception\SourceOfTransactionsDoNotExist as SourceOfTransactionsDoNotExistException;
use CommissionTask\Exception\SourceOfTransactionsDoNotMatchFormat as SourceOfTransactionsDoNotMatchFormatException;
use CommissionTask\Factory\Currency as CurrencyFactory;
use CommissionTask\Factory\Customer as CustomerFactory;
use CommissionTask\Factory\NewTransaction as NewTransactionFactory;
use CommissionTask\Factory\Rate as RateFactory;
use CommissionTask\Factory\Transaction as TransactionFactory;
use CommissionTask\Factory\TransactionCommission as TransactionCommissionFactory;
use CommissionTask\Repository\Currency as CurrencyRepository;
use CommissionTask\Repository\Rate as RateRepository;
use CommissionTask\Repository\Transaction as TransactionRepository;
use CommissionTask\Service\Commission as CommissionService;
use CommissionTask\Service\Currency as CurrencyService;
use CommissionTask\Service\Math as MathService;
use CommissionTask\Service\Rate as RateService;
use CommissionTask\Service\Transaction as TransactionService;
use CommissionTask\Service\TransactionOperation as TransactionOperationService;

//@todo add DI autowire
// Init Repositories
$currencyRepository = new CurrencyRepository();
$rateRepository = new RateRepository();
$transactionRepository = new TransactionRepository();

// Init Factories
$transactionCommissionFactory = new TransactionCommissionFactory();
$newTransactionFactory = new NewTransactionFactory();
$transactionFactory = new TransactionFactory();
$customerFactory = new CustomerFactory();
$currencyFactory = new CurrencyFactory();
$rateFactory = new RateFactory();

// Init Services
$mathService = new MathService();
$transactionService = new TransactionService($transactionRepository);
$rateService = new RateService(new MathService(8), $rateRepository, $rateFactory);
$currencyService = new CurrencyService($rateService, $currencyRepository, $currencyFactory);
$commissionService = new CommissionService($currencyService, $transactionService);
$transactionOperationService = new TransactionOperationService(
    $commissionService,
    $transactionRepository,
    $transactionCommissionFactory,
    $transactionFactory,
    $customerFactory,
    $currencyFactory
);

$transactionsSource = null;

if (isset($argc) && $argc === 2) {
    $transactionsSource = __DIR__ . "/{$argv[1]}";
}

if (file_exists($transactionsSource)) {
    $fileHandle = fopen($transactionsSource, 'r');

    while ( ! feof($fileHandle)) {
        $raw = fgetcsv($fileHandle);

        if ($raw) {
            if (count($raw) === 6) {
                $newTransactionDto = $newTransactionFactory->createFromArray($raw);

                $transactionOperationService->processTransaction($newTransactionDto);
            }else {
                throw new SourceOfTransactionsDoNotMatchFormatException();
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
