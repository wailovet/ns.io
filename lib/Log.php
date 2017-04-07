<?php
namespace Nsio;


class Log
{
    public static $callback;

    public static function d($tag, $data)
    {
        $callback = self::$callback;
        if ($callback) {
            $callback($tag, $data);
        }
    }
}