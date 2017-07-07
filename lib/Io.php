<?php
namespace Nsio;

class Io
{


    public static $excision = "\r\n";

    private $id;

    public $is_connect = false;

    public function __construct(ConnectionInterface $message_child)
    {
        $this->message_child = $message_child;
        $this->id = time() . rand(100000, 999999);
        Group::getInstance()->pushGlobal($this);

        $self = $this;
        $this->message_child->receive(function ($data_all) use ($self) {
            $data_array = explode(self::$excision, $data_all);
            echo "data_all:" . json_encode($data_all) . "\n";
            for ($i = 0; $i < count($data_array); $i++) {
                $data = $data_array[$i];
                if (empty($data)) {
                    continue;
                }


                Log::d("receive", $data);
                if (is_string($data)) {
                    $array_data = json_decode($data, true);
                } else {
                    $array_data = $data;
                }
                if (!isset($array_data['event'])) {
                    echo "receive:" . json_encode($data) . "  ???? \n";
                    continue;
                }
                $event_name = isset($array_data['event']) ? $array_data['event'] : "def";
                $event_callback = $self->getEvent("on_{$event_name}");
                !isset($array_data['data']) && $array_data['data'] = null;
                $event_callback && $event_callback($array_data['data']);
                $self->receive_event_callback && ($self->receive_event_callback)($data);


            }


        });

        $this->is_connect = true;
        $this->message_child->onClose(function () use ($self) {
            $self->is_connect = false;
            $event_callback = $self->getEvent("disconnect");
            $event_callback && $event_callback();
            Group::getInstance()->removeGlobal($self->getId());
        });

    }

    private $events = array();

    public $ext = array();

    /**
     * 返回事件回调
     * @param $name
     * @return callable
     */
    public function getEvent($name)
    {
        if (isset($this->events[$name])) {
            $callback = $this->events[$name];
        } else {
            $callback = function () {

            };
        }
        return $callback;
    }


    /**
     * 绑定事件，可能会引起事件名冲突，请使用$this->on()
     * @param string $event_name
     * @param callable $callback
     */
    public function event($event_name, $callback)
    {
        $this->events[$event_name] = $callback;
    }

    /**
     * 绑定事件
     * @param string $event_name
     * @param callable $callback
     */
    public function on($event_name, $callback)
    {
        $last_callback = $this->getEvent("on_{$event_name}");
        $this->event("on_{$event_name}", function ($data) use ($last_callback, $callback) {
            if ($last_callback) {
                $result = $last_callback($data);
                if ($result) {
                    return $result;
                }
            }
            if ($callback) {
                $result = $callback($data);
                if ($result) {
                    return $result;
                }
            }
        });
    }


    /**
     * 断开连接事件
     * @param callable $callback
     */
    public function disconnect($callback)
    {
        $last_callback = $this->getEvent("disconnect");
        $this->event("disconnect", function () use ($last_callback, $callback) {
            if ($last_callback) {
                $last_callback();
            }
            if ($callback) {
                $callback();
            }
        });
    }


    /**
     * 主动断开连接
     */
    public function close()
    {
        Group::getInstance()->removeGlobal($this->getId());
        $this->message_child->close();
    }


    /**
     * 向客户端发起事件信号
     * @param string $event_name
     * @param mixed $message
     */
    public function emit($event_name, $message)
    {
        $data = array(
            "event" => $event_name,
            "data" => $message,
        );
        Log::d("send", json_encode($data, true));
        $this->send($data);
    }


    /**
     * 加入一个组
     * @param string $group_name
     */
    public function join($group_name)
    {
        Group::getInstance()->pushIo($group_name, $this);
    }

    /**
     * 离开一个组
     * @param string $group_name
     */
    public function leave($group_name)
    {
        Group::getInstance()->removeIo($group_name, $this->getId());
    }

    /**
     * 返回唯一标识
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * 获取连接的其它信息
     */
    public function getInfo()
    {
        return $this->message_child->info();
    }


    /**
     * 负责底层通讯，ConnectionInterface接口的实现
     * @var ConnectionInterface message_child
     */
    private $message_child;


    public $receive_event_callback;

    /**
     * 抓包底层数据
     * @param $callback
     */
    public function receiveEvent($callback)
    {
        $this->receive_event_callback = $callback;
    }


    /**
     * 底层数据发送
     * @param $data
     */
    public function send($data)
    {
        return $this->message_child->send(json_encode($data) . self::$excision);
    }


}