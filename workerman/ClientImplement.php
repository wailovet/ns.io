<?php
use Nsio\ClientInterface;
use Workerman\Connection\AsyncTcpConnection;

class ClientImplement implements ClientInterface
{

    /** @var AsyncTcpConnection $tcp_connect */
    protected $tcp_connect;

    public function connect($host, $port)
    {
        $this->tcp_connect = new AsyncTcpConnection("{$host}:{$port}");
        $this->tcp_connect->connect();
    }

    public function send($data)
    {
        return $this->tcp_connect->send(json_encode($data, true));
    }

    public function receive($callback)
    {
        $this->tcp_connect->onMessage = function ($connect, $data) use ($callback) {
            if (isset($callback)) {
                $callback(json_decode($data, true));
            }
        };
    }

    public function close($callback)
    {
        $this->tcp_connect->onClose = function () use ($callback) {
            if (isset($callback)) {
                $callback();
            }
        };
    }
}