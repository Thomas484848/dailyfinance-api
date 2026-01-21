<?php

namespace App\Service\StockImport;

final class ValueCaster
{
    public static function toString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }

    public static function toFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace([',', '%'], '', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    public static function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace([',', '%'], '', $value);
        }

        return is_numeric($value) ? (int) $value : null;
    }

    public static function toDateTime(mixed $value): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (new \DateTimeImmutable())->setTimestamp((int) $value);
        }

        try {
            return new \DateTimeImmutable((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }
}
