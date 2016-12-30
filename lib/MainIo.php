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

    private $host = "0.0.0.0";
    private $port;

    /**
     * 通讯协议的实现
     * @var BaseMessageInterface main_message
     */
    public $main_message;

    public function __construct($port)
    {
        if (!empty($port)) {
            $this->port = $port;
        }
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }


    public function connection($callback)
    {
        $this->main_message->connect(function (BaseMessageChildInterface $message_child) use ($callback) {
            $io = new Io($message_child);
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