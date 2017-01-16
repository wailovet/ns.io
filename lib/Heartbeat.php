<?php
namespace Nsio;


class Heartbeat
{
    public $enter_flag = false;
    public $timeout_count = 0;
    public $max_timeout_count = 3;
    public $time_id = 0;
    public $io;

    public function __construct(Io $io)
    {
        $this->io = $io;
    }

    public function enter($timeout = 8000)
    {
        $that = $this;

        $this->time_id = Timer::setInterval(function () use ($that) {
            if (empty($that->time_id)) {
                echo "time_id is empty \n";
                return;
            }
            if (!$that->enter_flag) {
                $that->timeout_count++;
                echo "_HEARTBEAT:timeout_count {$that->io->getId()} {$that->timeout_count}\n";
            } else {
                $that->enter_flag = false;
                $that->timeout_count = 0;
            }

            if ($that->timeout_count >= $that->max_timeout_count) {
                $that->io->close();
                Timer::clearInterval($that->time_id);
                echo "_HEARTBEAT:enter_timeout {$that->time_id}\n";
            }
        }, $timeout);
        $this->io->disconnect(function () use ($that) {
            Timer::clearInterval($that->time_id);
        });
        $this->io->on("_HEARTBEAT", function () use ($that) {
            $that->enter_flag = true;
        });
    }
}