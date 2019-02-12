<?php

namespace msng\TimedId\Interfaces;

interface GeneratorInterface
{
    /**
     * @param \DateTimeInterface $dateTime
     * @return int
     */
    public function generate(\DateTimeInterface $dateTime): int;
}
