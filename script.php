<?php

require __DIR__ . '/vendor/autoload.php';

use CommissionTask\Container;
use CommissionTask\Exception\Transaction\SourceOfTransactionsDoNotMatchFormat as SourceOfTransactionsDoNotMatchFormatException;
use CommissionTask\Factory\FileSystem\FileReader as FileReaderFactory;
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

$fileReader = $container->get(FileReaderFactory::class)->createIterable($transactionsSource);

foreach ($fileReader->readByRaws($transactionsSource) as $raw) {
    if ($raw) {
        $newTransactionDto = $newTransactionFactory->createFromArray($raw);

        if ($processTransactionValidator->isValid($newTransactionDto->toArray())) {
            $transactionOperationService->processTransaction($newTransactionDto);
        } else {
            throw new SourceOfTransactionsDoNotMatchFormatException($processTransactionValidator->getFirstError());
        }
    }
}

foreach ($transactionRepository->getAll() as $transaction) {
    print_r($transaction->getCommission() . PHP_EOL);
}
