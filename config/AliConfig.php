<?php
/*
 * @Description   阿里相关配置
 * @Author        lifetime
 * @Date          2020-12-10 08:45:38
 * @LastEditTime  2020-12-13 22:30:19
 * @LastEditors   lifetime
 */

namespace service\config;

class AliConfig extends BasicConfig
{
    protected $config = [
        'sandbox' => false, // 是否是沙箱
    ];
    public function __construct($config = [])
    {
        if (class_exists("think\\facade\\Config")) {
            $config = \think\facade\Config::pull('ali');
        }
        parent::__construct(array_merge($this->config, $config));
    }
}