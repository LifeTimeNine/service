<?php
/*
 * @Description   配置基类
 * @Author        lifetime
 * @Date          2020-12-09 22:36:36
 * @LastEditTime  2021-07-15 14:28:02
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
    protected static $config;

    /**
     * 初始化的配置
     * @var array
     */
    public static $initConfig = [];

    /**
     * 配置字段
     * @var string
     */
    public static $key = 'service';

    /**
     * Config constructor.
     */
    public function __construct()
    {
        if (empty(self::$config)) {
            // 如果是 初始化的配置 就不在从配置文件中获取了
            if (!empty(self::$initConfig)) {
                self::$config = array_merge([], self::$initConfig);
            } else {
                self::$config = array_merge([], $this->getUserConfig(self::$key));
            }
            
            if (!empty(self::$config['cache_path'])) Cache::$cache_path = self::$config['cache_path'];

            if (!empty(self::$config['cache_callable']) && is_array(self::$config['cache_callable'])) Cache::$cache_callable = array_merge(Cache::$cache_callable, self::$config['cache_callable']);
        }
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
            return self::$config = array_merge(self::$config, $data);
        }
        return array_merge(self::$config, $data);
    }

    /**
     * 设置配置项值
     * @param string $offset
     * @param string|array|null|integer $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            self::$config[] = $value;
        } else {
            self::$config[$offset] = $value;
        }
    }

    /**
     * 判断配置Key是否存在
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset(self::$config[$offset]);
    }

    /**
     * 清理配置项
     * @param string|null $offset
     */
    public function offsetUnset($offset)
    {
        if (is_null($offset)) {
            self::$config = [];
        } else {
            unset(self::$config[$offset]);
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
            return self::$config;
        }
        return isset(self::$config[$offset]) ? self::$config[$offset] : $default;
    }

    /**
     * 获取用户配置
     * @param   string  $field  配置字段
     * @return  array   配置信息
     */
    public function getUserConfig($field)
    {
        // 兼容 tp5.1 和 tp6.0
        if (class_exists("think\\facade\\Config")) {
            if (is_callable([\think\Config::class, 'pull'])) {
                return \think\facade\Config::pull($field);
            } else {
                return \think\facade\Config::get($field);
            }
        }
        // 兼容 tp5.0
        if (class_exists("think\\Config")) {
            return \think\Config::get($field);
        }
        // 兼容 laravel
        if (class_exists("Illuminate\\Support\Facades\\Config")) {
            return \Illuminate\Support\Facades\Config::get($field);
        }
        return [];
    }
}