<?php
use Nsio\ClientInterface;
use Workerman\Connection\AsyncTcpConnection;

class ClientImplement implements ClientInterface
{

    /** @var AsyncTcpConnection $tcp_connect */
    protected $tcp_connect;

    private $host;
    private $port;
    private $protocol;

    /**
     * 初始化
     * ServerInterface constructor.
     * @param $protocol
     * @param $host
     * @param $port
     */
    public function __construct($protocol, $host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->protocol = $protocol;
    }

    public function connect()
    {
        $this->tcp_connect = new AsyncTcpConnection("{$this->protocol}://{$this->host}:{$this->port}");
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


    public function host()
    {
        return $this->host;
    }

    public function port()
    {
        return $this->port;
    }

    public function protocol()
    {
        return $this->protocol;
    }

}