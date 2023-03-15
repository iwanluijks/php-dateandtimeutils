<?php
declare(strict_types=1);

namespace IwanLuijks\PhpDateAndTimeUtils\TheTime;

use Iterator;

class TimePeriod implements Iterator
{
    public const EXCLUDE_START_DATE = 1;

    private Time $start;
    private TimeInterval $interval;
    private Time $end;
    private int $options;
    private int $stepsTaken;
    private Time $current;

    public function __construct(Time $start, TimeInterval $interval, Time $end, int $options = 0)
    {
        if ($start->diff($end)->invert === true) {
            throw new \InvalidArgumentException('Cannot step from start to end if end is greater than start.');
        }

        $this->start = $start;
        $this->interval = $interval;
        $this->end = $end;
        $this->stepsTaken = 0;
        $this->options = $options;

        if ($this->options === 0 || !($this->options & self::EXCLUDE_START_DATE)) {
            $this->current = $start;
        } else {
            $this->current = $start->add($this->interval);
        }
    }

    public function current(): Time
    {
        return $this->current;
    }

    public function key(): int
    {
        return $this->stepsTaken;
    }

    public function next(): void
    {
        $this->current = $this->current->add($this->interval);
        $this->stepsTaken += 1;
    }

    public function rewind(): void
    {
        if ($this->options === 0 || !($this->options & self::EXCLUDE_START_DATE)) {
            $this->current = $this->start;
        } else {
            $this->current = $this->start->add($this->interval);
        }

        $this->stepsTaken = 0;
    }

    public function valid(): bool
    {
        return $this->current->diff($this->end)->invert === false;
    }
}
