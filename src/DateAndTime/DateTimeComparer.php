<?php
declare(strict_types=1);

namespace IwanLuijks\PhpDateAndTimeUtils\DateAndTime;

class DateTimeComparer
{
    public const COMPARE_FORMAT_UNTIL_MINUTES = 'YmdHi|';

    /**
     * Comparison method useful for sorting.
     */
    public static function compare(\DateTimeInterface $dateTime1, \DateTimeInterface $dateTime2): int
    {
        if ($dateTime1 < $dateTime2) {
            return -1;
        } elseif ($dateTime1 > $dateTime2) {
            return 1;
        }

        return 0;
    }

    public static function compareUntil(\DateTimeInterface $dateTime1, \DateTimeInterface $dateTime2, string $until): int
    {
        $format1 = (int) $dateTime1->format($until);
        $format2 = (int) $dateTime2->format($until);

        return $format1 <=> $format2;
    }
}
