<?php

namespace App\Services;

use InvalidArgumentException;

/** Exact NGN arithmetic in integer kobo; store/display as decimal strings. */
final class Money
{
    public static function toKobo(string|int|float $amount): int
    {
        $input = trim((string) $amount);
        if (! preg_match('/^\d+(?:\.\d{1,2})?$/D', $input)) {
            throw new InvalidArgumentException('An amount must be a non-negative number with at most two decimals.');
        }
        [$naira, $kobo] = array_pad(explode('.', $input, 2), 2, '');
        if (strlen($naira) > 12) {
            throw new InvalidArgumentException('The amount is too large.');
        }
        return ((int) $naira * 100) + (int) str_pad($kobo, 2, '0');
    }

    public static function fromKobo(int $kobo): string
    {
        $sign = $kobo < 0 ? '-' : '';
        $value = abs($kobo);
        return $sign.intdiv($value, 100).'.'.str_pad((string) ($value % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function calculate(string $basic, string $allowance, string $deduction): array
    {
        $gross = self::toKobo($basic) + self::toKobo($allowance);
        $net = $gross - self::toKobo($deduction);
        if ($net < 0) {
            throw new InvalidArgumentException('Deductions cannot exceed total earnings.');
        }
        return ['gross' => self::fromKobo($gross), 'net' => self::fromKobo($net)];
    }
}
