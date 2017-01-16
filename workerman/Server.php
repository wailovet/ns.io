<?php
use Nsio\MainIo;

class Server
{
    public static function createTcp($port)
    {
        BaseMessage::$message_protocol = "tcp";
        $main_io = new MainIo(new BaseMessage("0.0.0.0", $port));
        return $main_io;
    }

    public static function createUdp($port)
    {
        BaseMessage::$message_protocol = "udp";
        $main_io = new MainIo(new BaseMessage("0.0.0.0", $port));
        return $main_io;
    }

    public static function createHttp($port)
    {
        BaseMessage::$message_protocol = "http";
        $main_io = new MainIo(new BaseMessage("0.0.0.0", $port));
        return $main_io;
    }

    public static function createWebsocket($port)
    {
        BaseMessage::$message_protocol = "websocket";
        $main_io = new MainIo(new BaseMessage("0.0.0.0", $port));
        return $main_io;
    }


}