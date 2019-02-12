<?php

namespace msng\TimedId\Values;

abstract class IntValue
{
    /**
     * @var
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getBinValue()
    {
        return decbin($this->value);
    }
}
