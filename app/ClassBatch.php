<?php
declare(strict_types=1);

namespace App;

use DateTimeImmutable;
use RuntimeException;

final class ClassBatch
{
    public const WEEKDAYS = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];

    public static function all(bool $activeOnly = false): array
    {
        return Database::fetchAll(
            'SELECT * FROM class_batches' . ($activeOnly ? ' WHERE active=1' : '') . ' ORDER BY start_date DESC, id DESC'
        );
    }

    public static function findActive(string $code): ?array
    {
        return Database::fetchOne('SELECT * FROM class_batches WHERE batch_code=? AND active=1', [$code]);
    }

    public static function save(array $data, int $id = 0): int
    {
        if ($id > 0) {
            Database::update('class_batches', $data, 'id=:id', ['id' => $id]);
            return $id;
        }
        return Database::insert('class_batches', $data);
    }

    public static function delete(int $id): void
    {
        Database::delete('class_batches', 'id=:id', ['id' => $id]);
    }

    public static function holidays(?string $start = null, ?string $end = null): array
    {
        if ($start !== null && $end !== null) {
            return Database::fetchAll(
                'SELECT * FROM class_holidays WHERE holiday_date BETWEEN ? AND ? ORDER BY holiday_date, id',
                [$start, $end]
            );
        }
        return Database::fetchAll('SELECT * FROM class_holidays ORDER BY holiday_date DESC, id DESC LIMIT 500');
    }

    public static function saveHoliday(string $date, string $title): int
    {
        return Database::insert('class_holidays', ['holiday_date' => $date, 'title' => $title]);
    }

    public static function deleteHoliday(int $id): void
    {
        Database::delete('class_holidays', 'id=:id', ['id' => $id]);
    }

    public static function endDate(string $startDate, int $durationMonths): DateTimeImmutable
    {
        $start = new DateTimeImmutable($startDate);
        $targetMonth = $start->modify('first day of this month')->modify('+' . $durationMonths . ' months');
        $day = min((int) $start->format('j'), (int) $targetMonth->format('t'));
        return $targetMonth->setDate(
            (int) $targetMonth->format('Y'),
            (int) $targetMonth->format('n'),
            $day
        )->modify('-1 day');
    }

    public static function calendar(array $batch): array
    {
        $start = new DateTimeImmutable($batch['start_date']);
        $end = self::endDate($batch['start_date'], (int) $batch['duration_months']);
        $holidayRows = self::holidays($start->format('Y-m-d'), $end->format('Y-m-d'));
        $holidays = [];
        foreach ($holidayRows as $holiday) {
            $holidays[$holiday['holiday_date']] = $holiday['title'];
        }
        $weekdays = array_fill_keys(
            array_filter(array_map('intval', explode(',', (string) $batch['class_weekdays']))),
            true
        );

        $months = [];
        $month = $start->modify('first day of this month');
        $lastMonth = $end->modify('first day of this month');
        while ($month <= $lastMonth) {
            $days = array_fill(0, (int) $month->format('N') - 1, null);
            $daysInMonth = (int) $month->format('t');
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $month->setDate((int) $month->format('Y'), (int) $month->format('n'), $day);
                $dateKey = $date->format('Y-m-d');
                $inRange = $date >= $start && $date <= $end;
                $holiday = $inRange ? ($holidays[$dateKey] ?? null) : null;
                $days[] = [
                    'date' => $dateKey,
                    'day' => $day,
                    'friday' => (int) $date->format('N') === 5,
                    'in_range' => $inRange,
                    'holiday' => $holiday,
                    'class' => $inRange && isset($weekdays[(int) $date->format('N')]) && $holiday === null,
                ];
            }
            while (count($days) % 7 !== 0) $days[] = null;
            $months[] = ['label' => $month->format('F Y'), 'days' => $days];
            $month = $month->modify('first day of next month');
        }

        $classDays = 0;
        $cursor = $start;
        while ($cursor <= $end) {
            $dateKey = $cursor->format('Y-m-d');
            if (isset($weekdays[(int) $cursor->format('N')]) && !isset($holidays[$dateKey])) {
                $classDays++;
            }
            $cursor = $cursor->modify('+1 day');
        }

        return [
            'start' => $start,
            'end' => $end,
            'months' => $months,
            'holidays' => $holidayRows,
            'class_days' => $classDays,
            'weekdays' => array_intersect_key(self::WEEKDAYS, $weekdays),
        ];
    }
}
