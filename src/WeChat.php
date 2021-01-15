<?php
/*
 * @Description   微信相关接口
 * @Author        lifetime
 * @Date          2021-01-15 17:13:59
 * @LastEditTime  2021-01-15 17:17:22
 * @LastEditors   lifetime
 */

namespace service;

use service\wechat\Official;
use service\wechat\Pay;
use service\wechat\PayV3;

/**
 * 微信相关接口
 */
class WeChat
{
    /**
     * 微信支付v2
     * @param   array   $config     配置信息
     * @return  Pay
     */
    public static function pay(array $config = [])
    {
        return Pay::instance($config);
    }

    /**
     * 微信支付v3
     * @param   array   $config     配置信息
     * @return   PayV3
     */
    public static function payV3(array $config = [])
    {
        return PayV3::instance($config);
    }

    /**
     * 微信开放平台
     * @param   array   $config     配置信息
     * @return  Official
     */
    public static function official(array $config = [])
    {
        return Official::instance($config);
    }
}