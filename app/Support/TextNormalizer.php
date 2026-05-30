<?php

namespace App\Support;

use Illuminate\Support\Str;

class TextNormalizer
{
    public static function normalize(?string $value): string
    {
        $value = trim((string) $value);
        $value = Str::ascii($value);
        $value = mb_strtolower($value, 'UTF-8');

        return preg_replace('/\s+/', ' ', $value) ?? '';
    }

    public static function contains(?string $haystack, ?string $needle): bool
    {
        $needle = self::normalize($needle);

        if ($needle === '') {
            return true;
        }

        return str_contains(self::normalize($haystack), $needle);
    }
}
