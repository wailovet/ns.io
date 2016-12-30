<?php
use Nsio\Group;
require_once("./workerman/autoload.php");

$io = Server::createWebsocket(12306);
$io->connection(function ($ioi) use ($io) {

    echo "connection \n";
    /** @var \Nsio\Io $i */
    $i = $ioi;

    $i->on("group", function ($data) use ($i, $io) {

        $i->join($data);
        $io->toGroup($data)->emit("count", Group::getInstance()->count($data));
        $io->emit("count", Group::getInstance()->count());

        $i->on("message", function ($msg) use ($i, $io, $data) {
            $io->toGroup($data)->filter(array($i->getId()))->emit("message", $msg);
        });
    });
    $i->disconnect(function () {
        echo "count:" . Group::getInstance()->count() . "\n";
        echo "disconnect \n";
    });
});
$io->run();