<?php
use Workerman\Worker;

/**
 * Created by PhpStorm.
 * User: wailovet
 * Date: 16/12/29
 * Time: 下午10:47
 */
class BaseMessage implements BaseMessageInterface
{
    public $worker;
    public static $message_protocol = "websocket";

    public function __construct($host, $port)
    {
        $this->worker = new Worker(BaseMessage::$message_protocol . "://{$host}:{$port}");
    }

    public function connect($callback)
    {
        $this->worker->onConnect = function ($connection) use ($callback) {
            $callback && $callback(new BaseMessageChild($connection));
        };
    }

    public static function run()
    {
        Worker::runAll();
    }
}