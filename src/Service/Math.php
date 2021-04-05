<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;

class Math
{
    public const ROUNDING_MODE = RoundingMode::UP;
    protected const DEFAULT_CURRENCY = 'EUR';

    private $roundingMode;
    private $context;

    public function __construct(int $scale = 2, int $roundingMode = self::ROUNDING_MODE)
    {
        $this->context = new CustomContext($scale);
        $this->roundingMode = $roundingMode;
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     *
     * @return string
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function add(string $leftOperand, string $rightOperand): string
    {
        $leftOperand = Money::of($leftOperand, self::DEFAULT_CURRENCY, $this->context, $this->roundingMode);
        $rightOperand = Money::of($rightOperand, self::DEFAULT_CURRENCY, $this->context, $this->roundingMode);

        return (string) $leftOperand->plus($rightOperand, self::ROUNDING_MODE)->getAmount();
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     *
     * @return string
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function divide(string $leftOperand, string $rightOperand)
    {
        $leftOperand = Money::of($leftOperand, self::DEFAULT_CURRENCY, $this->context, $this->roundingMode);
        $rightOperand = Money::of($rightOperand, self::DEFAULT_CURRENCY, $this->context, $this->roundingMode);

        return (string) $leftOperand->dividedBy($rightOperand, $this->roundingMode)->getAmount();
    }
}
