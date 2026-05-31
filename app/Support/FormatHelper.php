<?php

namespace App\Support;

class FormatHelper
{
    /**
     * Format a number for display: if it's an integer value show no decimals,
     * otherwise show the provided number of decimals.
     * Uses Vietnamese separators: thousands '.' and decimals ','.
     *
     * @param mixed $value
     * @param int $decimals
     * @return string
     */
    public static function number($value, int $decimals = 2): string
    {
        if (!is_numeric($value)) {
            return (string) $value;
        }

        $float = (float) $value;

        // If the value has no fractional part, show zero decimals
        $useDecimals = (floor($float) == $float) ? 0 : $decimals;

        return number_format($float, $useDecimals, ',', '.');
    }
}
