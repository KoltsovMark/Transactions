<?php

declare(strict_types=1);

namespace CommissionTask\Service;

use Brick\Math\BigRational;
use Brick\Math\RoundingMode;

class Math
{
    public const ROUNDING_MODE = RoundingMode::UP;

    private $scale;
    private $roundingMode;

    public function __construct(int $scale = 2, int $roundingMode = self::ROUNDING_MODE)
    {
        $this->scale = $scale;
        $this->roundingMode = $roundingMode;
    }

    public function add(string $leftOperand, string $rightOperand): string
    {
        return (string) BigRational::of($leftOperand)
            ->plus($rightOperand)
            ->toScale($this->scale, $this->roundingMode)
            ;
    }

    public function divide(string $leftOperand, string $rightOperand): string
    {
        return (string) BigRational::of($leftOperand)
            ->dividedBy($rightOperand)
            ->toScale($this->scale, $this->roundingMode)
            ;
    }
}
