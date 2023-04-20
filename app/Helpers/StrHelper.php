<?php

namespace App\Helpers;

class StrHelper
{
    public static function trimLower(string $val): string
    {
        return trim(mb_strtolower($val));
    }
}
