<?php

namespace App\Support;

class PhoneNormalizer
{
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        return '62' . $digits;
    }

    public static function looksLikePhone(string $value): bool
    {
        return (bool) preg_match('/^\+?[0-9]{6,}$/', trim($value));
    }
}
