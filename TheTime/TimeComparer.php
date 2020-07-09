<?php
declare(strict_types=1);

namespace IwanLuijks\PhpDateAndTimeUtils\TheTime;

class TimeComparer
{
    /**
     * Comparison method useful for sorting.
     */
    public static function compare(Time $time1, Time $time2): int
    {
        if ($time1->isBefore($time2)) {
            return -1;
        } elseif ($time1->isAfter($time2)) {
            return 1;
        } else {
            return 0;
        }
    }
}
