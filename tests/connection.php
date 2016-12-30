<?php
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$con = socket_connect($socket, '127.0.0.1', 12306);
if (!$con) {
    socket_close($socket);
    exit;
}
echo "Link\n";

socket_write($socket, json_encode(array(
    "event" => "group",
    "data" => $argv[1]
)));
while ($con) {
    $hear = socket_read($socket, 1024);
    echo $hear;
}
socket_shutdown($socket);
socket_close($socket);