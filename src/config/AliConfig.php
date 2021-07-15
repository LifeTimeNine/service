<?php
/*
 * @Description   阿里相关配置
 * @Author        lifetime
 * @Date          2020-12-10 08:45:38
 * @LastEditTime  2021-07-15 14:26:11
 * @LastEditors   lifetime
 */

namespace service\config;

/**
 * 阿里(支付宝)配置
 */
class AliConfig extends BasicConfig
{
    protected $defaultConfig = [
        'sandbox' => false, // 是否是沙箱,
        'format' => 'JSON', // 仅支持JSON
        'charset' => 'UTF-8', // 请求使用的编码格式
        'sign_type' => 'RSA2', // 商户生成签名字符串所使用的签名算法类型
        'version' => '1.0', // 调用的接口版本
    ];
    public function __construct($config = [])
    {
        parent::__construct();
        self::$config = array_merge($this->defaultConfig, $this->get('ali', []), $config);
    }
}