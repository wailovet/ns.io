<?php
namespace Nsio;


class Group
{


    private static $instance;

    /**
     * 单例模式，维护全局的分组信息
     * @return Group
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Group();
        }
        return self::$instance;
    }

    private $group_map;

    public function __construct()
    {
        $this->group_map = array(
            "all" => array()
        );
    }

    /**
     * 去掉分组中一个
     * @param $name
     * @param $id
     */
    private function remove($name, $id)
    {
        unset($this->group_map[$name][$id]);
    }

    private function get($name)
    {
        if (!isset($this->group_map[$name])) {
            $result = array();
        } else {
            $result = $this->group_map[$name];
        }
        if (empty($result)) {
            $result = array();
        }
        return $result;
    }

    private function push($name, Io $io)
    {
        if (!isset($this->group_map[$name])) {
            $this->group_map[$name] = array();
        }
        $io_id = $io->getId();
        $this->group_map[$name][$io_id] = $io;
    }

    public function count($name = "")
    {
        if (empty($name)) {
            if(!isset($this->group_map['all'])){
                return 0;
            }
            return count($this->group_map['all']);
        } else {
            if(!isset($this->group_map["item_{$name}"])){
                return 0;
            }
            return count($this->group_map["item_{$name}"]);
        }
    }

    public function pushGlobal(Io $io)
    {
        $this->push("all", $io);
    }

    public function pushIo($name, Io $io)
    {
        $this->push("item_{$name}", $io);
    }

    public function removeGlobal($id)
    {
        $group_map = $this->group_map;
        foreach ($group_map as $key => $value) {
            $this->remove($key, $id);
        }
    }

    public function removeIo($name, $id)
    {
        $this->remove("item_{$name}", $id);
    }

    public function getGlobal()
    {
        return $this->get("all");
    }

    public function getIo($name)
    {
        return $this->get("item_{$name}");
    }


}