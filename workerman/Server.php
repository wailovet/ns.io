<?php
use Nsio\MainIo;
use Nsio\Timer;
use Workerman\Worker;

class Server
{

    public static function createTcp($port)
    {
        ServerImplement::$message_protocol = "tcp";
        $main_io = new MainIo(new ServerImplement("0.0.0.0", $port));
        self::startGateway($main_io);
        return $main_io;
    }

    public static function createUdp($port)
    {

        ServerImplement::$message_protocol = "udp";
        $main_io = new MainIo(new ServerImplement("0.0.0.0", $port));
        return $main_io;
    }

    public static function createHttp($port)
    {
        ServerImplement::$message_protocol = "http";
        $main_io = new MainIo(new ServerImplement("0.0.0.0", $port));
        return $main_io;
    }

    public static function createWebsocket($port)
    {
        ServerImplement::$message_protocol = "websocket";
        $websocket = new ServerImplement("0.0.0.0", $port);
        $websocket->worker->count = 4;
        $main_io = new MainIo($websocket);
        self::startGateway($main_io);
        return $main_io;
    }

    public static function createWorker($name, $callback)
    {
        $worker = new Worker();
        $worker->name = $name;
        $worker->count = 1;
        $worker->onWorkerStart = function () use ($callback) {
            isset($callback) && $callback();
        };

        return $worker;
    }


    //开启转发网关
    public static function startGateway(MainIo $main_io)
    {
        $main_io->onStart(function () use ($main_io) {
            $client = new ClientImplement();
            $client->connect("tcp://127.0.0.1", 3196);
            $client->receive(function ($data) use ($main_io) {
                if (isset($data['group']) && isset($data['event'])) {
                    $main_io->toGroup($data['group'])->emit($data['event'], $data['data']);
                }
            });
            $client->close(function () use ($client) {
                $client->connect("tcp://127.0.0.1", 3196);
            });
            $main_io->emitByGroupEvent = function ($group_name, $event_name, $message) use ($client) {
                $client->send(array(
                    "group" => $group_name,
                    "event" => $event_name,
                    "data" => $message,
                ));
            };

        });
    }

    public static function runGateway()
    {
        $worker = new Worker("tcp://127.0.0.1:3196");
        $worker->name = 'Gateway';
        $worker->onMessage = function ($connection, $data) use ($worker) {
            foreach ($worker->connections as $connection_item) {
                if ($connection->id != $connection_item->id) {
                    $connection_item->send($data);
                }
            }
        };
    }

    public static function timeoutConfig()
    {
        Timer::setTimeoutFunction(function ($call_fun, $time) {
            return \Workerman\Lib\Timer::add($time / 1000, $call_fun, array(), false);
        });
        Timer::setIntervalFunction(function ($call_fun, $time) {
            return \Workerman\Lib\Timer::add($time / 1000, $call_fun);
        }, function ($timer_id) {
            \Workerman\Lib\Timer::del($timer_id);
        });
    }


    public static function init()
    {
        //开启多线程网关转发
        self::runGateway();

        //定时器实现
        self::timeoutConfig();
    }
}

Server::init();
