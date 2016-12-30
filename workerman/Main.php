<?php
use Nsio\MainIo;

/**
 * Created by PhpStorm.
 * User: wailovet
 * Date: 16/12/29
 * Time: 下午10:46
 */
class Main
{
    public static function createTcp($port)
    {
        BaseMessage::$message_protocol = "tcp";
        $main_io = new MainIo($port);
        $main_io->main_message = new BaseMessage("0.0.0.0", $port);
        return $main_io;
    }

    public static function createUdp($port)
    {
        BaseMessage::$message_protocol = "udp";
        $main_io = new MainIo($port);
        $main_io->main_message = new BaseMessage("0.0.0.0", $port);
        return $main_io;
    }

    public static function createWebsocket($port)
    {
        BaseMessage::$message_protocol = "websocket";
        $main_io = new MainIo($port);
        $main_io->main_message = new BaseMessage("0.0.0.0", $port);
        return $main_io;
    }


}