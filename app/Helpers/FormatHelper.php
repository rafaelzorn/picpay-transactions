<?php

namespace App\Helpers;

use Carbon\Carbon;
use DateTime;

class FormatHelper
{
    /**
     * @param DateTime $dateTime
     * @param string $format
     *
     * @return string
     */
    public static function formatMysqlDateTime(DateTime $dateTime, string $format = 'd/m/Y H:i:s'): string
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $dateTime)->format($format);
    }

    /**
     * @param float $value
     *
     * @return string
     */
    public static function formatMoneyToBrl(float $value): string
    {
        return number_format($value, 2, ',', '.');
    }
}
