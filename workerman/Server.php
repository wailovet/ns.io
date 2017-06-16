<?php
use Nsio\Gateway;
use Nsio\MainIo;
use Nsio\Timer;
use Workerman\Worker;

class Server
{


    /** @var Gateway $gateway */
    public static $gateway;

    public static function createTcp($port)
    {
        $main_io = new MainIo(new ServerImplement("tcp", "0.0.0.0", $port));
        isset(self::$gateway) && Gateway::access($main_io, new ClientImplement("text", "127.0.0.1", 3196));
        return $main_io;
    }

    public static function createUdp($port)
    {

        $main_io = new MainIo(new ServerImplement("udp", "0.0.0.0", $port));
        return $main_io;
    }

    public static function createHttp($port)
    {
        $main_io = new MainIo(new ServerImplement("http", "0.0.0.0", $port));
        return $main_io;
    }

    public static function createWebsocket($port)
    {
        $websocket = new ServerImplement("websocket", "0.0.0.0", $port);
        $websocket->worker->count = 8;
        $main_io = new MainIo($websocket);
        isset(self::$gateway) && Gateway::access($main_io, new ClientImplement("text", "127.0.0.1", 3196));
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


    public static function runGateway()
    {
        self::$gateway = Gateway::created(new ServerImplement("text", "127.0.0.1", 3196));
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
