<?php
declare(strict_types=1);

namespace IwanLuijks\PhpDateAndTimeUtils\TheTime;

class TimeInterval
{
    public ?int $h = null;
    public ?int $i = null;
    public ?int $s = null;

    public function __construct(?string $intervalSpec = null)
    {
        if ($intervalSpec !== null) {
            $matches = [];
            preg_match('/^PT(([0-9]|1[0-9]|2[0-4])H)?(([0-9]|[1-5][0-9]|60)M)?(([0-9]|[1-5][0-9]|60)S)?$/D', $intervalSpec, $matches);

            if (!isset($matches[2]) && !isset($matches[4]) && !isset($matches[6])) {
                throw new \InvalidArgumentException('Interval spec is not valid.');
            }

            if (isset($matches[2])) {
                $this->h = (int) $matches[2];
            }
            if (isset($matches[4])) {
                $this->i = (int) $matches[4];
            }
            if (isset($matches[6])) {
                $this->s = (int) $matches[6];
            }
        }
    }
}
