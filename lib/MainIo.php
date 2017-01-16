<?php
/**
 * Created by PhpStorm.
 * User: wailovet
 * Date: 16/12/29
 * Time: 下午9:09
 */

namespace Nsio;


use BaseMessageChildInterface;
use BaseMessageInterface;

class MainIo
{


    /**
     * 通讯协议的实现
     * @var BaseMessageInterface main_message
     */
    private $main_message;
    private $is_heartbeat;

    public function __construct($main_message, $is_heartbeat = false)
    {
        $this->main_message = $main_message;
        $this->is_heartbeat = $is_heartbeat;
    }

    public function connection($callback)
    {
        $is_heartbeat = $this->is_heartbeat;
        $this->main_message->connect(function (BaseMessageChildInterface $message_child) use ($callback, $is_heartbeat) {
            $io = new Io($message_child);
            if ($is_heartbeat) {
                $heartbeat = new Heartbeat($io);
                $heartbeat->enter();
            }
            $callback && $callback($io);
        });
    }


    private $group_name;

    public function toGroup($group_name)
    {
        $this->group_name = $group_name;
        return $this;
    }

    private $filter_id;

    public function filter($id = array())
    {
        for ($i = 0; $i < count($id); $i++) {
            $this->filter_id[$id[$i]] = true;
        }
        return $this;
    }

    public function emit($event_name, $message)
    {
        if (empty($this->group_name)) {
            $map = Group::getInstance()->getGlobal();
        } else {
            $map = Group::getInstance()->getIo($this->group_name);
        }
        /** @var Io $item */
        foreach ($map as $item) {
            $id = $item->getId();
            if (!$this->filter_id[$id]) {
                $item->emit($event_name, $message);
            }
        }
        $this->group_name = null;
        $this->filter_id = array();
    }


    public function run()
    {
        $this->main_message->run();
    }

}