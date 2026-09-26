<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;

/**
 * High-accuracy astronomical conversion between Gregorian Calendar (GC)
 * and Ethiopian Calendar (EC / ዓ.ም - ዓመተ ምሕረት).
 *
 * Implements the Kudlek-Beyene algorithm supporting leap year Pagume 5/6 variations.
 */
class EthiopianCalendarService
{
    public const ETHIOPIC_EPOCH = 1723856;

    public const AMHARIC_MONTHS = [
        1 => 'መስከረም',
        2 => 'ጥቅምት',
        3 => 'ኅዳር',
        4 => 'ታኅሣሥ',
        5 => 'ጥር',
        6 => 'የካቲት',
        7 => 'መጋቢት',
        8 => 'ሚያዝያ',
        9 => 'ግንቦት',
        10 => 'ሰኔ',
        11 => 'ሐምሌ',
        12 => 'ነሐሴ',
        13 => 'ጳጉሜን',
    ];

    public const ENGLISH_MONTHS = [
        1 => 'Meskerem',
        2 => 'Tikimt',
        3 => 'Hidar',
        4 => 'Tahsas',
        5 => 'Tir',
        6 => 'Yekatit',
        7 => 'Megabit',
        8 => 'Miyazya',
        9 => 'Ginbot',
        10 => 'Sene',
        11 => 'Hamle',
        12 => 'Nehase',
        13 => 'Pagume',
    ];

    public const AMHARIC_DAYS = [
        0 => 'እሑድ',
        1 => 'ሰኞ',
        2 => 'ማክሰኞ',
        3 => 'ረቡዕ',
        4 => 'ሐሙስ',
        5 => 'ዓርብ',
        6 => 'ቅዳሜ',
    ];

    public const ENGLISH_DAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    /**
     * Converts a Gregorian date into full Ethiopian Calendar (EC) metadata.
     *
     * @param CarbonInterface|DateTimeInterface|string|null $date
     * @return array{
     *     year: int,
     *     month: int,
     *     day: int,
     *     month_name_am: string,
     *     month_name_en: string,
     *     day_name_am: string,
     *     day_name_en: string,
     *     formatted_am: string,
     *     formatted_en: string,
     *     iso: string,
     *     is_leap_year: bool
     * }
     */
    public static function toEthiopian(CarbonInterface|DateTimeInterface|string|null $date = null): array
    {
        $carbon = self::parseCarbon($date);
        $jdn = self::gregorianToJdn(
            (int) $carbon->format('Y'),
            (int) $carbon->format('n'),
            (int) $carbon->format('j')
        );

        $jdnDiff = $jdn - self::ETHIOPIC_EPOCH;
        $n = $jdnDiff % 1461;
        $ethYear = 4 * intdiv($jdnDiff, 1461) + intdiv($n, 365) - intdiv($n, 1460);
        $dayOfYear = ($n % 365) + 365 * intdiv($n, 1460);

        $ethMonth = intdiv($dayOfYear, 30) + 1;
        $ethDay = ($dayOfYear % 30) + 1;

        $dayOfWeek = (int) $carbon->format('w');
        $isLeap = self::isLeapYear($ethYear);

        $monthAm = self::AMHARIC_MONTHS[$ethMonth] ?? '';
        $monthEn = self::ENGLISH_MONTHS[$ethMonth] ?? '';
        $dayAm = self::AMHARIC_DAYS[$dayOfWeek] ?? '';
        $dayEn = self::ENGLISH_DAYS[$dayOfWeek] ?? '';

        return [
            'year' => $ethYear,
            'month' => $ethMonth,
            'day' => $ethDay,
            'month_name_am' => $monthAm,
            'month_name_en' => $monthEn,
            'day_name_am' => $dayAm,
            'day_name_en' => $dayEn,
            'formatted_am' => "{$monthAm} {$ethDay}, {$ethYear} ዓ.ም",
            'formatted_en' => "{$monthEn} {$ethDay}, {$ethYear} EC",
            'iso' => sprintf('%04d-%02d-%02d', $ethYear, $ethMonth, $ethDay),
            'is_leap_year' => $isLeap,
        ];
    }

    /**
     * Converts an Ethiopian date to a Gregorian Carbon instance.
     */
    public static function toGregorian(int $ethiopianYear, int $ethiopianMonth, int $ethiopianDay): Carbon
    {
        $jdn = self::ETHIOPIC_EPOCH + 365 * $ethiopianYear + intdiv($ethiopianYear, 4) + 30 * $ethiopianMonth + $ethiopianDay - 31;
        [$year, $month, $day] = self::jdnToGregorian($jdn);

        return Carbon::createFromDate($year, $month, $day)->startOfDay();
    }

    /**
     * Formats a date into an Ethiopian string.
     */
    public static function format(CarbonInterface|DateTimeInterface|string|null $date = null, string $locale = 'am'): string
    {
        $ec = self::toEthiopian($date);
        return $locale === 'am' ? $ec['formatted_am'] : $ec['formatted_en'];
    }

    /**
     * Returns a dual-calendar representation (e.g. "26 Sep 2026 (መስከረም 16, 2019 ዓ.ም)")
     * commonly required in Ethiopian engineering contracts and progress billings.
     */
    public static function formatDual(CarbonInterface|DateTimeInterface|string|null $date = null, string $gcFormat = 'd M Y'): string
    {
        $carbon = self::parseCarbon($date);
        $ec = self::toEthiopian($carbon);

        return "{$carbon->format($gcFormat)} ({$ec['formatted_am']})";
    }

    /**
     * Checks if the given Ethiopian year is a leap year (Pagume has 6 days).
     */
    public static function isLeapYear(int $ethiopianYear): bool
    {
        return ($ethiopianYear % 4) === 3;
    }

    /**
     * Returns days in a given Ethiopian month (30 for months 1-12, 5 or 6 for Pagume).
     */
    public static function daysInMonth(int $ethiopianYear, int $ethiopianMonth): int
    {
        if ($ethiopianMonth >= 1 && $ethiopianMonth <= 12) {
            return 30;
        }

        if ($ethiopianMonth === 13) {
            return self::isLeapYear($ethiopianYear) ? 6 : 5;
        }

        throw new \InvalidArgumentException("Invalid Ethiopian month: {$ethiopianMonth}");
    }

    /**
     * Convert Gregorian (Y, M, D) to Julian Day Number.
     */
    public static function gregorianToJdn(int $year, int $month, int $day): int
    {
        $a = intdiv(14 - $month, 12);
        $y = $year + 4800 - $a;
        $m = $month + 12 * $a - 3;

        return $day
            + intdiv(153 * $m + 2, 5)
            + 365 * $y
            + intdiv($y, 4)
            - intdiv($y, 100)
            + intdiv($y, 400)
            - 32045;
    }

    /**
     * Convert Julian Day Number to Gregorian [year, month, day].
     *
     * @return array{0: int, 1: int, 2: int}
     */
    public static function jdnToGregorian(int $jdn): array
    {
        $a = $jdn + 32044;
        $b = intdiv(4 * $a + 3, 146097);
        $c = $a - intdiv(146097 * $b, 4);
        $d = intdiv(4 * $c + 3, 1461);
        $e = $c - intdiv(1461 * $d, 4);
        $m = intdiv(5 * $e + 2, 153);

        $day = $e - intdiv(153 * $m + 2, 5) + 1;
        $month = $m + 3 - 12 * intdiv($m, 10);
        $year = 100 * $b + $d - 4800 + intdiv($m, 10);

        return [$year, $month, $day];
    }

    private static function parseCarbon(CarbonInterface|DateTimeInterface|string|null $date): Carbon
    {
        if ($date === null) {
            return Carbon::now();
        }

        if ($date instanceof Carbon) {
            return $date;
        }

        if ($date instanceof DateTimeInterface) {
            return Carbon::instance($date);
        }

        return Carbon::parse($date);
    }
}
