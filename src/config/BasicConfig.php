<?php
/*
 * @Description   配置基类
 * @Author        lifetime
 * @Date          2020-12-09 22:36:36
 * @LastEditTime  2021-01-09 19:49:03
 * @LastEditors   lifetime
 */

namespace service\config;

use ArrayAccess;
use service\tools\Cache;

class BasicConfig implements ArrayAccess
{
    /**
     * 当前配置值
     * @var array
     */
    protected $config;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->config = array_merge([], $this->getUserConfig('lifetime-service'));

        if (!empty($this->config['cache_path'])) Cache::$cache_path = $this->config['cache_path'];

        if (!empty($this->config['cache_callable']) && is_array($this->config['cache_callable'])) Cache::$cache_callable = array_merge(Cache::$cache_callable, $this->config['cache_callable']);
    }

    /**
     * 设置配置项值
     * @param string $offset
     * @param string|array|null|integer $value
     */
    public function set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    /**
     * 获取配置项参数
     * @param string|null $offset
     * @return array|string|null
     */
    public function get($offset = null, $default = null)
    {
        return $this->offsetGet($offset, $default);
    }

    /**
     * 合并数据到对象
     * @param array $data 需要合并的数据
     * @param bool $append 是否追加数据
     * @return array
     */
    public function merge(array $data, $append = false)
    {
        if ($append) {
            return $this->config = array_merge($this->config, $data);
        }
        return array_merge($this->config, $data);
    }

    /**
     * 设置配置项值
     * @param string $offset
     * @param string|array|null|integer $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->config[] = $value;
        } else {
            $this->config[$offset] = $value;
        }
    }

    /**
     * 判断配置Key是否存在
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /**
     * 清理配置项
     * @param string|null $offset
     */
    public function offsetUnset($offset)
    {
        if (is_null($offset)) {
            $this->config = [];
        } else {
            unset($this->config[$offset]);
        }
    }

    /**
     * 获取配置项参数
     * @param string|null $offset
     * @pram  mixed       $default
     * @return array|string|null
     */
    public function offsetGet($offset, $default = null)
    {
        if (is_null($offset)) {
            return $this->config;
        }
        return isset($this->config[$offset]) ? $this->config[$offset] : $default;
    }

    /**
     * 获取用户配置
     * @param   string  $field  配置字段
     * @return  array   配置信息
     */
    public function getUserConfig($field)
    {
        $config = [];
        if (class_exists("think\\facade\\Config")) {
            $config = \think\facade\Config::pull($field);
        }
        return $config;
    }
}