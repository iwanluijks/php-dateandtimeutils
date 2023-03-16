<?php
declare(strict_types=1);

namespace IwanLuijks\PhpDateAndTimeUtils\TheTime;

use Iterator;

class TimePeriod implements Iterator
{
    public const EXCLUDE_START_DATE = 1;
    public const INCLUDE_END_DATE = 2;

    private Time $start;
    private TimeInterval $interval;
    private Time $end;
    private int $options;
    private int $stepsTaken;
    private Time $current;
    private int $lowestTimeWeLooped;

    public function __construct(Time $start, TimeInterval $interval, Time $end, int $options = 0)
    {
        // We should Time->isAfter here:

        if ($start->isAfter($end)) {
            throw new \InvalidArgumentException('Cannot step from start to end if start is greater than end.');
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

        $this->lowestTimeWeLooped = $this->current->getComposite();
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
        $this->stepsTaken = 0;

        if ($this->options === 0 || !($this->options & self::EXCLUDE_START_DATE)) {
            $this->current = $this->start;
        } else {
            $this->current = $this->start->add($this->interval);
        }

        $this->lowestTimeWeLooped = $this->current->getComposite();
    }

    public function valid(): bool
    {
        if ($this->stepsTaken > 0 && $this->lowestTimeWeLooped === $this->current->getComposite()) {
            return false;
        }

        $comparison = TimeComparer::compare($this->current, $this->end);

        if ($comparison === -1) {
            return true;
        } elseif ($comparison === 0) {
            // When include end date, we should get here for 23:59 on PT1M, but we don't...
            if ($this->options | self::INCLUDE_END_DATE) {
                return true;
            }
        }

        return false;
    }
}
