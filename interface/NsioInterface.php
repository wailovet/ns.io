<?php
namespace Nsio;

interface ServerInterface
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
     * @param $callback
     * @return mixed
     */
    public function start($callback);

    /**
     * 运行
     * @return mixed
     */
    public static function run();


    public function info();
}

interface ConnectionInterface
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

interface ClientInterface
{
    public function connect($host, $port);

    public function send($data);

    public function receive($callback);

}