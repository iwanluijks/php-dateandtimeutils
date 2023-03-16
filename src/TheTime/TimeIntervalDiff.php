<?php
declare(strict_types=1);

namespace IwanLuijks\PhpDateAndTimeUtils\TheTime;

use DateTimeZone;

class TimeIntervalDiff extends TimeInterval
{
    /**
     * True when the object diffed against is greater, otherwise false.
     */
    public ?bool $invert;
    public ?DateTimeZone $tzFrom;
    public ?DateTimeZone $tzTo;

    /**
     * Returns the total number of whole hours that is different between compared times.
     */
    public function getHours(): int
    {
        return intdiv($this->getSeconds(), 3600);
    }

    /**
     * Returns the total number of whole minutes that is different between compared times.
     */
    public function getMinutes(): int
    {
        return intdiv($this->getSeconds(), 60);
    }

    /**
     * Returns the total number of seconds that is different between compared times.
     */
    public function getSeconds(): int
    {
        $h = $this->h ?? 0;
        $i = $this->i ?? 0;
        $s = $this->s ?? 0;
        return ($h * 60 * 60) + ($i * 60) + $s;
    }
}
