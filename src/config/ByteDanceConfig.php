<?php
/*
 * @Description   字节跳动 配置
 * @Author        lifetime
 * @Date          2020-12-23 08:49:42
 * @LastEditTime  2020-12-23 12:28:38
 * @LastEditors   lifetime
 */
namespace service\config;

/**
 * 字节跳动 配置
 */
class ByteDanceConfig extends BasicConfig
{
    protected $config = [
        'cache_path' => '', // 缓存目录
        'miniapp_appid' => '', // 字节小程序APPID
        'miniapp_secret' => '', // 字节小程序APP Secret
    ];

    public function __construct($config = [])
    {
        parent::__construct(array_merge($this->config, $this->getUserConfig('toutiao'), $config));
    }
}