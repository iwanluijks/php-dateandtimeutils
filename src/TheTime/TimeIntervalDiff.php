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
}
