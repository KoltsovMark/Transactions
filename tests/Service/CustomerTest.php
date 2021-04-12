<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use CommissionTask\Service\Customer\Customer as CustomerService;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    /**
     * @covers \CommissionTask\Service\Customer::isSupportedCustomerType
     *
     * @dataProvider dataProviderForIsSupportedCustomerType
     */
    public function testIsSupportedCustomerType(string $customerType, bool $expectation)
    {
        $this->assertEquals($expectation, CustomerService::isSupportedCustomerType($customerType));
    }

    /**
     * @return array[]
     */
    public function dataProviderForIsSupportedCustomerType()
    {
        return [
            'legal type is supported' => ['legal', true],
            'natural type is supported' => ['natural', true],
            'temporary type is not supported' => ['temporary', false],
        ];
    }
}