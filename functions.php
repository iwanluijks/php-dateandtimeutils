<?php
declare(strict_types=1);

namespace IwanLuijks\PhpDateAndTimeUtils;

/**
 * Returns the first value in the given parameters that isn't int(0).
 *
 * @param array $values
 * @return mixed
 */
function coalesceZero(...$values)
{
    $filtered = array_filter(
        $values, function ($val) {
        return $val !== 0;
    }
    );
    reset($filtered);

    return current($filtered) ?: 0;
}
