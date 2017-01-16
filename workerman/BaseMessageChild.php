<?php

/**
 * Created by PhpStorm.
 * User: wailovet
 * Date: 16/12/29
 * Time: 下午10:47
 */
class BaseMessageChild implements BaseMessageChildInterface
{

    /** @var \Workerman\Connection\TcpConnection $connection */
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function send($data)
    {
        $this->connection->send($data);
    }


    public function receive($callback)
    {
        $this->connection->onMessage = function ($connection, $data) use ($callback) {
            $callback && $callback($data);
        };
    }

    public function close()
    {
        $this->connection->close();
    }

    public function onClose($callback)
    {
        $this->connection->onClose = function ($connection) use ($callback) {
            $callback && $callback();
        };
        $this->connection->onError = function ($connection) use ($callback) {
            $callback && $callback();
        };
    }


    public function info()
    {
        return array(
            "ip" => $this->connection->getRemoteIp(),
            "port" => $this->connection->getRemotePort(),
        );
    }
}