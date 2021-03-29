<?php

namespace CommissionTask\Contract;

use CommissionTask\Contract\Model as ModelInterface;

interface Repository
{
    /**
     * @return object[]
     */
    public function getAll();
}