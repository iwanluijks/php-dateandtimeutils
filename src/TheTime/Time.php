<?php
declare(strict_types=1);

namespace IwanLuijks\PhpDateAndTimeUtils\TheTime;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use IwanLuijks\PhpContracts\Contract;
use function IwanLuijks\PhpDateAndTimeUtils\coalesceZero;

class Time
{
    private string $composite;
    private array $parts = [
        'hour' => null,
        'minute' => null,
        'second' => null
    ];
    private string $partsSeparator = ':';
    private string $timeSpec;
    private DateTimeZone $timezone;

    public function __construct(string $timeSpec, ?DateTimeZone $timezone = null)
    {
        if ($timezone === null) {
            $timezone = new DateTimeZone(date_default_timezone_get());
        }
        $this->parseTimeSpec($timeSpec, $timezone);
        $this->timeSpec = $timeSpec;
        $this->timezone = $timezone;
    }

    public function parseTimeSpec(string $timeSpec, DateTimeZone $timezone): void
    {
        // $parts[0] must not be lower than 0 and higher than 23.
        // $parts[1] must not be lower than 0 and higher than 59.
        // $parts[2] must not be lower than 0 and higher than 59.

        $parts = explode($this->partsSeparator, $timeSpec);
        $hour = (int) $parts[0];
        $minute = (int) $parts[1];
        $second = (int) ($parts[2] ?? 00);

        (new Contract('Hour MUST be a number starting from (and including) 0 up until (and including) 23.'))
                ->requires($hour >= 0 && $hour <= 23);
        (new Contract('Minute MUST be a number starting from (and including) 0 up until (and including) 59.'))
                ->requires($minute >= 0 && $minute <= 59);
        (new Contract('Second MUST be a number starting from (and including) 0 up until (and including) 59.'))
                ->requires($second >= 0 && $second <= 59);

        $this->parts['hour'] = $hour;
        $this->parts['minute'] = $minute;
        $this->parts['second'] = $second;
        $this->composite = (sprintf('%\'02d', $this->parts['hour']) ?: '00'); // We should always have 2 chars.
        $this->composite .= (sprintf('%\'02d', $this->parts['minute']) ?: '00'); // We should always have 2 chars.
        $this->composite .= (sprintf('%\'02d', $this->parts['second']) ?: '00'); // We should always have 2 chars.
        $this->composite .= '|TZ|' . $timezone->getName();
    }

    public static function createFromDateTime(DateTimeInterface $dateTime): self
    {
        return new Time($dateTime->format('H:i:s'), $dateTime->getTimezone());
    }

    public function add(TimeInterval $interval): self
    {
        $addHours = $interval->h;
        $addMinutes = $interval->i;
        $addSeconds = $interval->s;
        $hour = $this->getHour();
        $minute = $this->getMinute();
        $second = $this->getSecond();

        if ($second + $addSeconds >= 60) {
            $addMinutes += 1;
            $second = -60 + $second + $addSeconds;
        } else {
            $second += $addSeconds;
        }

        if ($minute + $addMinutes >= 60) {
            $addHours += 1;
            $minute = -60 + $minute + $addMinutes;
        } else {
            $minute += $addMinutes;
        }

        if ($hour + $addHours >= 24) {
            $hour = -24 + $hour + $addHours;
        } else {
            $hour += $addHours;
        }

        $spec = (sprintf('%\'02d', $hour) ?: '00'); // We should always have 2 chars.
        $spec .= $this->partsSeparator;
        $spec .= (sprintf('%\'02d', $minute) ?: '00'); // We should always have 2 chars.
        $spec .= $this->partsSeparator;
        $spec .= (sprintf('%\'02d', $second) ?: '00'); // We should always have 2 chars.

        return new Time($spec, clone $this->getTimezone());
    }

    public function constrainBackward(TimeInterval $timeInterval, bool $loseRest = false, self $minimumTime = null): self
    {
        $constrainToHour = $timeInterval->h > 0;
        $constrainToMinute = $timeInterval->i > 0;
        $constrainToSecond = $timeInterval->s > 0;
        $hour = $this->getHour();
        $minute = $this->getMinute();
        $second = $this->getSecond();

        if ($constrainToSecond === true) {
            $second = $timeInterval->s * (floor($second / $timeInterval->s));
        } else {
            if ($loseRest === true) {
                $second = 0;
            }
        }
        if ($constrainToMinute === true) {
            $minute = $timeInterval->i * (floor($minute / $timeInterval->i));
        } else {
            if (!$constrainToSecond && $loseRest === true) {
                $minute = 0;
            }
        }
        if ($constrainToHour === true) {
            $hour = $timeInterval->h * (floor($hour / $timeInterval->h));
        } else {
            if (!$constrainToMinute && !$constrainToSecond && $loseRest === true) {
                $hour = 0;
            }
        }

        $spec = (sprintf('%\'02d', $hour) ?: '00'); // We should always have 2 chars.
        $spec .= $this->partsSeparator;
        $spec .= (sprintf('%\'02d', $minute) ?: '00'); // We should always have 2 chars.
        $spec .= $this->partsSeparator;
        $spec .= (sprintf('%\'02d', $second) ?: '00'); // We should always have 2 chars.

        $builtTime = new Time($spec, clone $this->getTimezone());

        if ($minimumTime && $builtTime->isBefore($minimumTime)) {
            return $minimumTime;
        }

        return $builtTime;
    }

    public function diff(Time $time): TimeIntervalDiff
    {
        $timeInterval = new TimeIntervalDiff();
        $timeInterval->h = $time->getHour() - $this->getHour();
        $timeInterval->i = $time->getMinute() - $this->getMinute();
        $timeInterval->s = $time->getSecond() - $this->getSecond();
        $timeInterval->invert = false;
        $timeInterval->tzFrom = $time->getTimezone();
        $timeInterval->tzTo = $this->getTimezone();

        if ($time->getComposite() < $this->getComposite()) {
            $timeInterval->invert = true;
            $timeInterval->i = 60 - $timeInterval->i;
            $timeInterval->s = 60 - $timeInterval->s;

            if ($timeInterval->s > 0) {
                $timeInterval->i = abs($timeInterval->i) - 1;
            }
            if ($timeInterval->i > 0) {
                $timeInterval->h = abs($timeInterval->h) - 1;
            }
        }

        if ($timeInterval->s >= 60) {
            $timeInterval->s -= 60;
            $timeInterval->i += 1;
        }
        if ($timeInterval->i >= 60) {
            $timeInterval->i -= 60;
            $timeInterval->h += 1;
        }

        $timeInterval->h = abs($timeInterval->h);
        $timeInterval->i = abs($timeInterval->i);
        $timeInterval->s = abs($timeInterval->s);

        return $timeInterval;
    }

    /**
     * $format specification:
     * - %H for hour
     * - %i for minute
     * - %s for second
     */
    public function format(?string $format = null): string
    {
        $hour = (sprintf('%\'02d', $this->parts['hour']) ?: '00');
        $minute = (sprintf('%\'02d', $this->parts['minute']) ?: '00');
        $second = (sprintf('%\'02d', $this->parts['second']) ?: '00');

        if (!$format) {
            $format = '%H' . $this->partsSeparator . '%i' . $this->partsSeparator . '%s';
        }

        return strtr(
            $format,
            [
                '%H' => $hour,
                '%i' => $minute,
                '%s' => $second
            ]
        );
    }

    public function getComposite(): int
    {
        [$time, $timezone] = explode('|TZ|', $this->composite, 2);

        $datetime = (new DateTime($time, new DateTimeZone($timezone)));
        $datetime->setTimezone(new DateTimeZone('UTC'));

        return (int) $datetime->format('His');
    }

    public function getHour(): ?int
    {
        return $this->parts['hour'];
    }

    public function getMinute(): ?int
    {
        return $this->parts['minute'];
    }

    public function getSecond(): ?int
    {
        return $this->parts['second'];
    }

    public function getTimezone(): DateTimeZone
    {
        return $this->timezone;
    }

    public function isAfter(Time $time): bool
    {
        $diff = $this->diff($time);

        return $diff->invert && $diff->getSeconds() > 0;
    }

    public function isBefore(Time $time): bool
    {
        $diff = $this->diff($time);

        return !$diff->invert && $diff->getSeconds() > 0;
    }
}
