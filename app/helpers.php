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

if (!function_exists('faTOen')) {
    function faTOen($string): string
    {
        return strtr($string, array('۰'=>'0', '۱'=>'1', '۲'=>'2', '۳'=>'3', '۴'=>'4', '۵'=>'5', '۶'=>'6', '۷'=>'7', '۸'=>'8', '۹'=>'9', '٠'=>'0', '١'=>'1', '٢'=>'2', '٣'=>'3', '٤'=>'4', '٥'=>'5', '٦'=>'6', '٧'=>'7', '٨'=>'8', '٩'=>'9'));
    }
}

if (!function_exists('enToFa')) {
    function enToFa($string): string
    {
        return strtr($string, array('0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹'));
    }
}
