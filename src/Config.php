<?php
/*
 * @Description   配置管理类
 * @Author        lifetime
 * @Date          2021-07-15 13:45:23
 * @LastEditTime  2021-09-16 23:17:00
 * @LastEditors   lifetime
 */

namespace service;

/**
 * 配置管理类
 * @class Config
 */
class Config
{
    /**
     * 全局配置
     * @var array
     */
    protected static $gloablConfig = [];

    /**
     * 配置文件
     * @var string
     */
    protected static $configKey = 'service';

    /**
     * 初始化配置
     * @param   array   $config
     */
    public static function init(array $config = [])
    {
        self::$gloablConfig = $config;
    }

    /**
     * 获取所有配置
     * @return array
     */
    public static function all()
    {
        return self::$gloablConfig;
    }

    /**
     * 设置配置 Key
     * @param   string  $key
     */
    public static function setKey(string $key)
    {
        self::$configKey = $key;
    }

    /**
     * 获取配置 Key
     * @return  string
     */
    public static function getKey()
    {
        return self::$configKey;
    }
}
