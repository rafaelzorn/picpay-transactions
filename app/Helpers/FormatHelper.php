<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * @param string $string
     *
     * @return string
     */
    public static function onlyNumbers(string $string): string
    {
        return preg_replace('/[^0-9]/', '', $string);
    }
}
