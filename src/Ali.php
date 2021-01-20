<?php
/*
 * @Description   支付宝和阿里云相关接口
 * @Author        lifetime
 * @Date          2021-01-15 17:40:55
 * @LastEditTime  2021-01-18 17:22:19
 * @LastEditors   lifetime
 */

namespace service;

use service\ali\Oss;
use service\ali\Pay;

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
     * @param   array   $config     配置信息
     * @return  Oss
     */
    public static function oss()
    {
        return Oss::instance();
    }
}