<?php
/*
 * @Description   支付宝和阿里云相关接口
 * @Author        lifetime
 * @Date          2021-01-15 17:40:55
 * @LastEditTime  2021-10-18 13:44:27
 * @LastEditors   lifetime
 */

namespace service;

use service\ali\Oss;
use service\ali\Pay;
use service\ali\Push;
use service\ali\Sms;

/**
 * 支付宝和阿里云相关接口
 * @class Ali
 */
class Ali
{
    /**
     * 支付宝支付
     * @param   array   $config     配置参数
     * @return  Pay
     */
    public static function pay(array $config = [])
    {
        return Pay::instance($config);
    }

    /**
     * OSS相关操作
     * @param   array   $config     配置参数
     * @return  Oss
     */
    public static function oss(array $config = [])
    {
        return Oss::instance($config);
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

    /**
     * 移动推送服务
     * @return Push
     */
    public static function push()
    {
        return Push::instance();
    }
}