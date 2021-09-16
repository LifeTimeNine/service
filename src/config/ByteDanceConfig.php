<?php
/*
 * @Description   字节跳动 配置
 * @Author        lifetime
 * @Date          2020-12-23 08:49:42
 * @LastEditTime  2021-09-16 17:57:10
 * @LastEditors   lifetime
 */
namespace service\config;

/**
 * 字节跳动 配置
 */
class ByteDanceConfig extends BasicConfig
{
    protected $defaultConfig = [
        'cache_path' => '', // 缓存目录
        'miniapp_appid' => '', // 字节小程序APPID
        'miniapp_secret' => '', // 字节小程序APP Secret
    ];

    public function __construct($config = [])
    {
        parent::__construct();
        self::$config = array_merge($this->defaultConfig, self::$globalConfig['byteDance']??[], $config);
    }
}