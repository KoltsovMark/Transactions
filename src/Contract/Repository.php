<?php

declare(strict_types=1);

namespace CommissionTask\Contract;

interface Repository
{
    /**
     * @return object[]
     */
    public function getAll(): array;
}
