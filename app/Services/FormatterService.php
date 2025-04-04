<?php

namespace App\Services;

use Carbon\Carbon;

class FormatterService
{
    public static function formatMoney(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    public static function formatDate(string $date): string
    {
        return Carbon::parse($date)->format('d/m/Y H:i:s');
    }
}