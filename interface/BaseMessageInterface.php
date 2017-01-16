<?php

interface BaseMessageInterface
{

    /**
     * 初始化
     * BaseMessageInterface constructor.
     * @param $host
     * @param $port
     */
    public function __construct($host, $port);

    /**
     * 连接事件，传入一个BaseMessageChildInterface
     * @param $callback
     * @return mixed
     */
    public function connect($callback);

    /**
     * 运行
     * @return mixed
     */
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

    public function info();
}