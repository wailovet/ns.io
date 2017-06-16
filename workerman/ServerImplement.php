<?php
use Nsio\ServerInterface;
use Workerman\Worker;

class ServerImplement implements ServerInterface
{
    public $worker;
    public static $message_protocol = "websocket";


    private $host;
    private $port;
    private $protocol;

    public function __construct($protocol, $host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->protocol = $protocol;
        $this->worker = new Worker("{$protocol}://{$host}:{$port}");
    }

    public function connect($callback)
    {
        $this->worker->onConnect = function ($connection) use ($callback) {
            $callback && $callback(new ConnectionImplement($connection));
        };
    }

    /**
     * @param $callback
     * @return mixed
     */
    public function start($callback)
    {
        $this->worker->onWorkerStart = function () use ($callback) {
            $callback && $callback();
        };
    }


    public static function run()
    {
        Worker::runAll();
    }

    public function info()
    {
        return array(
            "id" => $this->worker->id,
            "count" => count($this->worker->connections)
        );
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