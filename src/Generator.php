<?php

namespace msng\TimedId;

use msng\TimedId\Interfaces\GeneratorInterface;
use msng\TimedId\Interfaces\SequenceHolderInterface;
use msng\TimedId\Values\DataCenterId;
use msng\TimedId\Values\WorkerId;

class Generator implements GeneratorInterface
{
    /**
     * @var int
     */
    protected $originTimestamp = 0;

    /**
     * @var int
     */
    protected $maxTotalBitLength = 63;

    /**
     * @var int
     */
    protected $timestampBitLength = 31;

    /**
     * @var int
     */
    protected $dataCenterIdBitLength = 5;

    /**
     * @var int
     */
    protected $workerIdBitLength = 5;

    /**
     * @var int
     */
    protected $sequenceBitLength = 22;

    /**
     * @var DataCenterId
     */
    private $dataCenterId;

    /**
     * @var WorkerId
     */
    private $workerId;

    /**
     * @var SequenceHolderInterface
     */
    private $sequenceHolder;

    /**
     * Generator constructor.
     *
     * @param SequenceHolderInterface $sequenceHolder
     * @param WorkerId $workerId
     * @param DataCenterId $dataCenterId
     */
    public function __construct(SequenceHolderInterface $sequenceHolder, WorkerId $workerId, DataCenterId $dataCenterId)
    {
        $this->checkLength();

        $this->setDataCenterId($dataCenterId);
        $this->setWorkerId($workerId);
        $this->sequenceHolder = $sequenceHolder;
    }

    /**
     * @param \DateTimeInterface $dateTime
     * @return int
     */
    public function generate(\DateTimeInterface $dateTime): int
    {
        $bin = '';

        $timestamp = $dateTime->getTimestamp() - $this->originTimestamp;
        $bin .= $this->zeroFilledBinary($timestamp, $this->timestampBitLength);

        $dataCenterIdBinary = $this->zeroFilledBinary($this->dataCenterId->getValue(), $this->dataCenterIdBitLength);
        $bin .= $dataCenterIdBinary;

        $workerIdBinary = $this->zeroFilledBinary($this->workerId->getValue(), $this->workerIdBitLength);
        $bin .= $workerIdBinary;

        $sequence = $this->zeroFilledBinary($this->sequenceHolder->getCurrentSequence($dateTime), $this->sequenceBitLength);
        $bin .= $sequence;

        return bindec($bin);
    }

    private function checkLength()
    {
        $totalLength = $this->timestampBitLength + $this->dataCenterIdBitLength + $this->workerIdBitLength + $this->sequenceBitLength;

        if ($totalLength > $this->maxTotalBitLength) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @param DataCenterId $dataCenterId
     */
    private function setDataCenterId(DataCenterId $dataCenterId)
    {
        if (strlen($dataCenterId->getBinValue()) > $this->dataCenterIdBitLength) {
            throw new \InvalidArgumentException();
        }

        $this->dataCenterId = $dataCenterId;
    }

    /**
     * @param WorkerId $workerId
     */
    private function setWorkerId(WorkerId $workerId)
    {
        if (strlen($workerId->getBinValue()) > $this->workerIdBitLength) {
            throw new \InvalidArgumentException();
        }

        $this->workerId = $workerId;
    }

    /**
     * @param int $decimal
     * @param int $length
     * @return string
     */
    private function zeroFilledBinary(int $decimal, int $length): string
    {
        $binary = decbin($decimal);

        if (strlen($binary) > $length) {
            throw new \InvalidArgumentException();
        }

        $format = '%0' . (string)$length . 's';

        return sprintf($format, $binary);
    }
}
