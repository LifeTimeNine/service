<?php
/*
 * @Description   配置管理类
 * @Author        lifetime
 * @Date          2021-07-15 13:45:23
 * @LastEditTime  2021-07-15 14:12:31
 * @LastEditors   lifetime
 */

namespace service;

use service\config\BasicConfig;

/**
 * 配置管理类
 * @class Config
 */
class Config
{
    /**
     * 初始化配置
     * @param   array   $config
     */
    public static function init(array $config = [])
    {
        BasicConfig::$initConfig = $config;
    }

    /**
     * 设置配置 Key
     * @param   string  $key
     */
    public static function setKey($key)
    {
        BasicConfig::$key = $key;
    }
}
