<?php

namespace App\Helpers;

class ValidationHelper
{
    /**
     * Taken from https://gist.github.com/rafael-neri/ab3e58803a08cb4def059fce4e3c0e40
     *
     * @param string $document
     *
     * @return bool
     */
    public static function isValidCpf(string $document): bool
    {
        $document = preg_replace( '/[^0-9]/is', '', $document);

        if (strlen($document) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $document)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $document[$c] * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($document[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Taken from https://gist.github.com/guisehn/3276302
     *
     * @param string $document
     *
     * @return bool
     */
    public static function isValidCnpj(string $document): bool
    {
        $document = preg_replace('/[^0-9]/is', '', $document);

        if (strlen($document) != 14) {
            return false;
        }

        if (preg_match('/(\d)\1{13}/', $document)) {
            return false;
        }

        for ($t = 12; $t < 14; $t++) {
            for ($d = 0, $m = ($t - 7), $i = 0; $i < $t; $i++) {
                $d += $document[$i] * $m;
                $m = ($m == 2 ? 9 : --$m);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($document[$i] != $d) {
                return false;
            }
        }

        return true;
    }
}
