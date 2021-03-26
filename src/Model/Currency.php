<?php

namespace CommissionTask\Model;

class Currency
{
    protected string $code;

    public function __construct(string $code)
    {
        $this->setCode($code);
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Currency
     */
    public function setCode(string $code)
    {
        $this->code = $code;
        return $this;
    }
}