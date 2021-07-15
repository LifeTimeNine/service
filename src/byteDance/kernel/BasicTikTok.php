<?php
/*
 * @Description   抖音 基类
 * @Author        lifetime
 * @Date          2020-12-23 09:19:28
 * @LastEditTime  2021-07-15 14:44:14
 * @LastEditors   lifetime
 */
namespace service\byteDance\kernel;

use service\config\ByteDanceConfig;

class BasicTouTiao
{
    /**
     * 配置
     * @var DataArray
     */
    protected $config;

    /**
     * 缓存
     * @var static
     */
    protected static $cache;

    /**
     * 构造函数
     * @param   array   $cofing     配置信息
     */
    protected function __construct($config = [])
    {
        $this->config = new ByteDanceConfig($config);
    }

    /**
     * 静态创建对象
     * @param   array   $config     配置信息
     * @return  static
     */
    public static function instance(array $config = [])
    {
        $key = md5(get_called_class() . serialize($config));
        if (isset(self::$cache[$key])) return self::$cache[$key];
        return self::$cache[$key] = new static($config);
    }
}