<?php

interface BaseMessageInterface
{
    public function __construct($host, $port);

    public function connect($callback);

    public static function run();
}

interface BaseMessageChildInterface
{
    public function send($data);

    /**
     * 绑定数据接收事件receive，传入data
     * @param $callback
     * @return mixed
     */
    public function receive($callback);

    public function close();

    public function onClose($callback);
}