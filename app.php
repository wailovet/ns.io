<?php
use Nsio\Io;

require_once __DIR__ . "/./autoload.php";

$http_server = Server::createHttp(1083);
$http_server->connection(function (Io $io) {
    $io->emit("message", "开始");
});

$websocket = Server::createWebsocket(1084);
$websocket->connection(function (Io $io) use ($websocket) {
    $io->on("hi", function ($data) use ($io, $websocket) {
        $websocket->emit("hi", $data);
    });
});

$tcp_socket = Server::createTcp(26688);
$tcp_socket->connection(function (ConnectionImplement $con){
    $con->connection->onMessage = function ($data){
        echo $data;
    };
});
$tcp_socket->run();