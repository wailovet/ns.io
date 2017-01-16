<?php
namespace Nsio;

class Timer
{


    private static $interval_function;
    private static $interval_clear_function;

    public static function setIntervalFunction($function, $clear_function)
    {
        self::$interval_function = $function;
        self::$interval_clear_function = $clear_function;
    }


    public static function setInterval($callback, $time_interval = 1000)
    {
        $timer_id = 0;
        if (self::$interval_function) {
            $fun = self::$interval_function;
            $timer_id = $fun($callback, $time_interval);
        }
        return $timer_id;
    }

    public static function clearInterval($timer_id)
    {
        if (self::$interval_clear_function) {
            $fun = self::$interval_clear_function;
            $fun($timer_id);
        }
    }
}