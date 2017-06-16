<?php
namespace Nsio;

class Gateway
{
    public static function created(ServerInterface $gateway_server)
    {
        $gateway = new MainIo($gateway_server);
        $gateway->connection(function (Io $io) use ($gateway) {
            $io->on("__proxy__", function ($data) use ($io, $gateway) {
                $gateway->filter(array($io->getId()))->emit("__proxy__", $data);
            });
        });
        return $gateway;
    }

    public static function access(MainIo $server, ClientInterface $client)
    {
        $server->onStart(function () use ($server, $client) {
            $client->connect();
            $client->receive(function ($data) use ($server) {
                if (isset($data['event']) && $data['event'] == "__proxy__") {
                    $result = $data['data'];
                    $server->gatewayEmit($result['group'], $result['event'], $result['data']);
                }
            });
            $client->close(function () use ($client) {
                $client->connect();
            });
        });
        $server->gateway_callback = function ($group_name, $event_name, $message) use ($client) {
            $client->send(array(
                "event" => "__proxy__",
                "data" => array(
                    "group" => $group_name,
                    "event" => $event_name,
                    "data" => $message,
                )
            ));
        };
    }


}