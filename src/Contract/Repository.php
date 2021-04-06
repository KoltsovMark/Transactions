<?php

namespace CommissionTask\Contract;

interface Repository
{
    /**
     * @return object[]
     */
    public function getAll();
}