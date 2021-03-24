<?php
declare(strict_types=1);

namespace IwanLuijks\PhpDateAndTimeUtils\TheDate;

class DateWithoutTime
{
    private array $parts = [
        'year' => null,
        'month' => null,
        'day' => null
    ];
    private string $partsSeparator = '-';

    public function __construct(string $dateSpec)
    {
        $this->parseDateSpec($dateSpec);
        $this->dateSpec = $dateSpec;
    }

    public function parseDateSpec(string $dateSpec): void
    {
        // $parts[0] must not be lower than 0 and higher than 9999.
        // $parts[1] must not be lower than 1 and higher than 12.
        // $parts[2] must not be lower than 1 and higher than 31.

        $parts = explode($this->partsSeparator, $dateSpec);
        $this->parts['year'] = (int) $parts[0];
        $this->parts['month'] = (int) $parts[1];
        $this->parts['day'] = (int) ($parts[2]);
        $this->composite = (sprintf('%\'04d', $this->parts['year']) ?: '00'); // We should always have 4 chars.
        $this->composite .= (sprintf('%\'02d', $this->parts['month']) ?: '00'); // We should always have 2 chars.
        $this->composite .= (sprintf('%\'02d', $this->parts['day']) ?: '00'); // We should always have 2 chars.
        $this->composite .= '|TZ|' . 'UNDEFINED';
    }
}
