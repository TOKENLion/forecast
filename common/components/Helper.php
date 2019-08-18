<?php
namespace common\components;

class Helper {

    public static function convertFahrenheitToCelsius($value)
    {
        return (($value - 32) * 5) / 9;
    }
}
?>