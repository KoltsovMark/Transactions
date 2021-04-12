<?php

require __DIR__ . '/vendor/autoload.php';

use CommissionTask\Container;
use CommissionTask\Exception\Transaction\SourceOfTransactionsDoNotExist as SourceOfTransactionsDoNotExistException;
use CommissionTask\Exception\Transaction\SourceOfTransactionsDoNotMatchFormat as SourceOfTransactionsDoNotMatchFormatException;
use CommissionTask\Factory\Transaction\NewTransaction as NewTransactionFactory;
use CommissionTask\Repository\Transaction\Transaction as TransactionRepository;
use CommissionTask\Service\Transaction\TransactionOperation as TransactionOperationService;
use CommissionTask\Validator\Transaction\ProcessTransaction as ProcessTransactionValidator;

$container = new Container();

$transactionRepository = $container->get(TransactionRepository::class);
$newTransactionFactory = $container->get(NewTransactionFactory::class);
$processTransactionValidator = $container->get(ProcessTransactionValidator::class);
$transactionOperationService = $container->get(TransactionOperationService::class);

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
