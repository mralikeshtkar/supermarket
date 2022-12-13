<?php

use Hekmatinasser\Verta\Verta;

if (!function_exists('to_valid_mobile_number')) {
    /**
     * Convert a mobile to valid format.
     *
     * @param string $mobile
     * @return string
     */
    function to_valid_mobile_number(string $mobile): string
    {
        return "+98" . substr($mobile, -10, 10);
    }
}

if (!function_exists('validateDate')) {
    function validateDate($value, $format = 'Y/m/d'): bool
    {
        try {
            Verta::parseFormat($format, $value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('jalaliFormat')) {
    function jalaliFormat($date, string $format = 'Y/m/d'): string
    {
        return verta($date)->format($format);
    }
}
