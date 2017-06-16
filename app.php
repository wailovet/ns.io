<?php
use Nsio\Io;

require_once __DIR__ . "/./autoload.php";

$server = Server::createHttp(80);
$server->connection(function (Io $io) {
    $io->emit("message", "开始");
});

