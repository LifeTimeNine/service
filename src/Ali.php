<?php
/*
 * @Description   支付宝和阿里云相关接口
 * @Author        lifetime
 * @Date          2021-01-15 17:40:55
 * @LastEditTime  2021-01-21 17:28:37
 * @LastEditors   lifetime
 */

namespace service;

use service\ali\Oss;
use service\ali\Pay;
use service\ali\Sms;

/**
 * 支付宝和阿里云相关接口
 * @class Ali
 */
class Ali
{
    /**
     * 支付宝支付
     * @return  Pay
     */
    public static function pay()
    {
        return Pay::instance();
    }

    /**
     * OSS相关操作
     * @return  Oss
     */
    public static function oss()
    {
        return Oss::instance();
    }

    /**
     * 短信服务
     * @param   array   $config     配置
     * @return  Sms
     */
    public static function sms(array $config = [])
    {
        return Sms::instance($config);
    }
}