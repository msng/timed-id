<?php

namespace msng\TimedId\Interfaces;

interface SequenceHolderInterface
{
    public function getCurrentSequence(\DateTimeInterface $dateTime): int;
}
