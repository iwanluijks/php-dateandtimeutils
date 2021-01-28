<?php
declare(strict_types=1);

namespace IwanLuijks\PhpDateAndTimeUtils;

use DateInterval;
use function intdiv;

class DateTimeIntervalDiffHelper
{
    private DateInterval $dateInterval;

    public function __construct(DateInterval $dateInterval)
    {
        $this->dateInterval = $dateInterval;
    }

    public function getYears(): int
    {
        return $this->dateInterval->y;
    }

    public function getQuarters(): int
    {
        return ($this->getYears() * 4) + intdiv($this->getMonths(), 3);
    }

    public function getMonths(): int
    {
        return ($this->getYears() * 12) + $this->dateInterval->m;
    }

    public function getWeeks(): int
    {
        return intdiv($this->getDays(), 7);
    }

    public function getDays(): int
    {
        return $this->dateInterval->days;
    }

    public function getHours(): int
    {
        return ($this->getDays() * 24) + $this->dateInterval->h;
    }

    public function getMinutes(): int
    {
        return ($this->getHours() * 60) + $this->dateInterval->i;
    }

    public function getSeconds(): int
    {
        return ($this->getMinutes() * 60) + $this->dateInterval->s;
    }
}
