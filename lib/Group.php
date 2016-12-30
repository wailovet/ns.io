<?php
/**
 * Created by PhpStorm.
 * User: wailovet
 * Date: 16/12/29
 * Time: 下午9:09
 */

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
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
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
        $result = $this->group_map[$name];
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
            return count($this->group_map['all']);
        } else {
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