<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use Brick\Math\Exception\DivisionByZeroException;
use PHPUnit\Framework\TestCase;
use CommissionTask\Service\Math;

class MathTest extends TestCase
{
    private Math $math;

    public function setUp()
    {
        $this->math = new Math();
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $expectation
     *
     * @covers \CommissionTask\Service\Math::add
     *
     * @dataProvider dataProviderForAddTesting
     */
    public function testAdd(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals($expectation, $this->math->add($leftOperand, $rightOperand));
    }

    /**
     * @return \string[][]
     */
    public function dataProviderForAddTesting(): array
    {
        return [
            'add 2 natural numbers' => ['1', '2', '3'],
            'add negative number to a positive' => ['-1', '2', '1'],
            'add natural number to a float' => ['1', '1.05123', '2.06'],
        ];
    }

    /**
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $expectation
     *
     * @covers \CommissionTask\Service\Math::divide
     *
     * @dataProvider dataProviderForDivideTesting
     */
    public function testDivide(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals($expectation, $this->math->divide($leftOperand, $rightOperand));
    }

    public function dataProviderForDivideTesting(): array
    {
        return [
            'divide 2 natural numbers' => ['6', '2', '3'],
            'divide natural number to a negative float' => ['-10', '2.5358', '-3.95'],
            'divide natural number to a float' => ['10', '2.5358', '3.95'],
            'divide negative natural number to a negative natural number' => ['-10', '-2', '5'],
        ];
    }

    /**
     * @covers \CommissionTask\Service\Math::divide
     */
    public function testDivideNaturalNumberToZero()
    {
        $this->expectException(DivisionByZeroException::class);

        $this->math->divide('10', '0');
    }
}
