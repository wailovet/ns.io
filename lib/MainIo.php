<?php
namespace Nsio;

class MainIo
{


    /**
     * 通讯协议的实现
     * @var ServerInterface main_message
     */
    private $main_message;

    public function __construct($main_message)
    {
        $this->main_message = $main_message;
    }

    public function onStart($callback)
    {
        $this->main_message->start(function () use ($callback) {
            $callback && $callback();
        });
    }

    public function connection($callback)
    {
        $this->main_message->connect(function (ConnectionInterface $message_child) use ($callback) {
            $io = new Io($message_child);
            $callback && $callback($io);
        });
    }


    protected $group_name;

    public function toGroup($group_name)
    {
        $this->group_name = $group_name;
        return $this;
    }

    protected $filter_id;

    public function filter($id = array())
    {
        for ($i = 0; $i < count($id); $i++) {
            $this->filter_id[$id[$i]] = true;
        }
        return $this;
    }


    public $gateway_callback;

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
            if (!isset($this->filter_id[$id])) {
                $item->emit($event_name, $message);
            }
        }
        if (isset($this->gateway_callback)) {
            ($this->gateway_callback)($this->group_name, $event_name, $message);
        }
        $this->group_name = null;
        $this->filter_id = array();
    }


    /**
     * 发送数据，用于底层调用
     * @param $data
     */
    public function send($data)
    {
        if (empty($this->group_name)) {
            $map = Group::getInstance()->getGlobal();
        } else {
            $map = Group::getInstance()->getIo($this->group_name);
        }
        /** @var Io $item */
        foreach ($map as $item) {
            $id = $item->getId();
            if (!isset($this->filter_id[$id])) {
                $item->send($data);
            }
        }
        $this->group_name = null;
        $this->filter_id = array();
    }

    /**
     * 网关发送的数据，用于底层调用
     * @param $group_name
     * @param $data
     */
    public function gatewaySend($group_name, $data)
    {
        if (empty($group_name)) {
            $map = Group::getInstance()->getGlobal();
        } else {
            $map = Group::getInstance()->getIo($group_name);
        }
        /** @var Io $item */
        foreach ($map as $item) {
            $item->send($data);
        }
    }

    /**
     * 网关发送的信号
     * @param $group_name
     * @param $event_name
     * @param $message
     */
    public function gatewayEmit($group_name, $event_name, $message)
    {
        if (empty($group_name)) {
            $map = Group::getInstance()->getGlobal();
        } else {
            $map = Group::getInstance()->getIo($group_name);
        }
        /** @var Io $item */
        foreach ($map as $item) {
            $item->emit($event_name, $message);
        }
    }

    public function getInfo()
    {
        return $this->main_message->info();
    }

    public function run()
    {
        $this->main_message->run();
    }


}