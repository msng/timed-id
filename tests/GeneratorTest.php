<?php

namespace msng\TimedId\Tests;

use msng\TimedId\Tests\Classes\SequenceHolder;
use msng\TimedId\Tests\Classes\TimedIdGenerator;
use msng\TimedId\Values\DataCenterId;
use msng\TimedId\Values\WorkerId;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $dateTime         = \DateTime::createFromFormat('Y-m-d H:i:s', '2019-01-01 12:34:56');
        $timestampBinary  = decbin($dateTime->getTimestamp());
        $dataCenterBinary = '00010';
        $workerIdBinary   = '00101';
        $sequenceBinary   = '0000000000000000000001';
        $binary           = $timestampBinary . $dataCenterBinary . $workerIdBinary . $sequenceBinary;
        $expected         = bindec($binary);

        $dataCenterId   = new DataCenterId(2);
        $workerId       = new WorkerId(5);
        $sequenceHolder = new SequenceHolder();
        $generator      = new TimedIdGenerator($sequenceHolder, $workerId, $dataCenterId);
        $actual         = $generator->generate($dateTime);

        $this->assertSame($expected, $actual);
    }

    public function testOverflow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $dateTime       = \DateTime::createFromFormat('Y-m-d H:i:s', '2020-12-31 00:11:22');
        $dataCenterId   = new DataCenterId(0);
        $workerId       = new WorkerId(33);
        $sequenceHolder = new SequenceHolder();
        $generator      = new TimedIdGenerator($sequenceHolder, $workerId, $dataCenterId);
        $generator->generate($dateTime);
    }

    /**
     * @throws \Exception
     */
    public function testMin()
    {
        $dateTime    = new \DateTime();
        $expectedBin = decbin($dateTime->getTimestamp()) . '00000000000000000000000000000000';
        $this->assertSame(63, strlen($expectedBin));

        $expected = bindec($expectedBin);
        $actual   = TimedIdGenerator::min($dateTime);

        $this->assertSame($expected, $actual);
    }
}
