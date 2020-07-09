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

    public function getQuarters(): int
    {
        return ($this->getYears() * 4) + intdiv($this->getMonths(), 3);
    }

    public function getYears(): int
    {
        return $this->dateInterval->y ?? 0;
    }

    public function getMonths(): int
    {
        $intervalMonths = $this->dateInterval->m ?? 0;
        return $this->getYears() * 12 + $intervalMonths;
    }

    public function getWeeks(): int
    {
        return intdiv($this->getDays(), 7);
    }

    public function getSeconds(): int
    {
        return $this->getMinutes() * 60;
    }

    public function getMinutes(): int
    {
        return $this->getHours() * 60;
    }

    public function getHours(): int
    {
        return $this->getDays() * 24;
    }

    public function getDays(): int
    {
        return $this->dateInterval->days ?? 0;
    }
}
