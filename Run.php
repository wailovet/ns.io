<?php
use Nsio\Group;
use Nsio\Io;

require_once("./workerman/autoload.php");

class BootData
{
    static $uuid_map = array();
    public $uuid;
//    static $id_map = array();
}

$server = Server::createWebsocket(12306);
$server->connection(function (Io $io) use ($server) {
    $boot_data = new BootData();
    $io->on("BOOT", function ($data) use ($io, $server, $boot_data) {
        $id = $io->getId();
        $boot_data->uuid = $uuid = $data['uuid'];
        $version = $data['version'];
        BootData::$uuid_map[$uuid] = array(
            "id" => $id,
            "uuid" => $uuid,
            "version" => $version,
            "is_start" => false
        );
    });

    $io->on("START_PROGRAM", function ($data) use ($io, $server, $boot_data) {
        BootData::$uuid_map[$boot_data->uuid]['is_start'] = true;
//        $uuid = $data['uuid'];
//        print_r($boot_data->uuid);
    });

    $io->on("ALL_BOOT_DATA", function ($data) use ($io, $server, $boot_data) {
        $io->emit("ALL_BOOT_DATA", BootData::$uuid_map);
    });

    $io->disconnect(function () use ($boot_data) {
        unset(BootData::$uuid_map[$boot_data->uuid]);
    });


});
$server->run();