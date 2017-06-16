<?php
namespace Nsio;

interface ServerInterface
{

    public function protocol();

    public function host();

    public function port();

    /**
     * 初始化
     * ServerInterface constructor.
     * @param $protocol
     * @param $host
     * @param $port
     */
    public function __construct($protocol, $host, $port);

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
    /**
     * 初始化
     * ServerInterface constructor.
     * @param $protocol
     * @param $host
     * @param $port
     */
    public function __construct($protocol, $host, $port);

    public function protocol();

    public function host();

    public function port();

    public function connect();

    public function send($data);

    public function receive($callback);

    public function close($callback);

}