<?php

namespace msng\TimedId\Tests\Classes;

use msng\TimedId\Interfaces\SequenceHolderInterface;

class SequenceHolder implements SequenceHolderInterface
{
    public function getCurrentSequence(\DateTimeInterface $dateTime): int
    {
        return 1;
    }
}
