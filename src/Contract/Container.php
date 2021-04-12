<?php

declare(strict_types=1);

namespace CommissionTask\Contract;

use CommissionTask\Exception\EntryNotFound as EntryNotFoundException;

interface Container
{
    /**
     * @throws EntryNotFoundException;
     */
    public function get(string $id): object;

    public function has(string $id): bool;
}
