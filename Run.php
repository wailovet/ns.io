<?php
use Nsio\Io;

require_once("./workerman/autoload.php");

$server = Server::createWebsocket(12306);
$server->connection(function (Io $io) use ($server) {
    $io->on("group", function ($data) use ($io, $server) {
        $io->join($data);

        $io->on("message", function ($msg) use ($io, $server, $data) {
            $server->toGroup($data)->filter(array($io->getId()))->emit("message", $msg);
        });
    });
    $io->disconnect(function () {
        echo "disconnect\n";
    });
});
$server->run();