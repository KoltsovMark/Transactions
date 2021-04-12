<?php

declare(strict_types=1);

namespace CommissionTask;

use CommissionTask\Contract\Container as ContainerInterface;
use CommissionTask\Exception\EntryNotFound as EntryNotFoundException;
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
use CommissionTask\Service\Configuration as ConfigurationService;
use CommissionTask\Service\Currency\Currency as CurrencyService;
use CommissionTask\Service\Math as MathService;
use CommissionTask\Service\Rate\Rate as RateService;
use CommissionTask\Service\Transaction\Transaction as TransactionService;
use CommissionTask\Service\Transaction\TransactionOperation as TransactionOperationService;
use CommissionTask\Validator\Transaction\ProcessTransaction as ProcessTransactionValidator;

class Container implements ContainerInterface
{
    private array $application;

    public function __construct()
    {
        $this->application = [
            // Init Configuration
            ConfigurationService::class => new ConfigurationService(),

            // Init Repositories
            CurrencyRepository::class => CurrencyRepository::getInstance(),
            RateRepository::class => RateRepository::getInstance(),
            TransactionRepository::class => TransactionRepository::getInstance(),

            // Init Factories
            TransactionFactory::class => new TransactionFactory(),
            CustomerFactory::class => new CustomerFactory(),
            CurrencyFactory::class => new CurrencyFactory(),
            RateFactory::class => new RateFactory(),
            CashInCommissionFactory::class => new CashInCommissionFactory(),
            CashOutLegalCommissionFactory::class => new CashOutLegalCommissionFactory(),
            CashOutNaturalCommissionFactory::class => new CashOutNaturalCommissionFactory(),
            NewTransactionFactory::class => new NewTransactionFactory(),
        ];

        // Init Services
        $this->application[MathService::class] = new MathService(8);
        $this->application[TransactionService::class] = new TransactionService(
            $this->get(TransactionRepository::class)
        );
        $this->application[RateService::class] = new RateService(
            $this->get(ConfigurationService::class),
            $this->get(MathService::class),
            $this->get(RateRepository::class),
            $this->get(RateFactory::class)
        );
        $this->application[CurrencyService::class] = new CurrencyService(
            $this->get(ConfigurationService::class),
            $this->get(RateService::class),
            $this->get(CurrencyRepository::class),
            $this->get(CurrencyFactory::class)
        );
        $this->application[CommissionService::class] = new CommissionService(
            $this->get(ConfigurationService::class),
            $this->get(CurrencyService::class),
            $this->get(TransactionService::class)
        );
        $this->application[TransactionOperationService::class] = new TransactionOperationService(
            $this->get(CommissionService::class),
            $this->get(TransactionRepository::class),
            $this->get(TransactionFactory::class),
            $this->get(CustomerFactory::class),
            $this->get(CurrencyFactory::class),
            $this->get(CashInCommissionFactory::class),
            $this->get(CashOutLegalCommissionFactory::class),
            $this->get(CashOutNaturalCommissionFactory::class)
        );

        // Init Validators
        $this->application[ProcessTransactionValidator::class] = new ProcessTransactionValidator(
            $this->get(CurrencyService::class)
        );
    }

    public function get(string $id): object
    {
        if ($this->has($id)) {
            return $this->application[$id];
        } else {
            throw new EntryNotFoundException();
        }
    }

    public function has(string $id): bool
    {
        return \array_key_exists($id, $this->application);
    }
}
