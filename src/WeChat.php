<?php
/*
 * @Description   微信相关接口
 * @Author        lifetime
 * @Date          2021-01-15 17:13:59
 * @LastEditTime  2021-09-28 09:06:09
 * @LastEditors   lifetime
 */

namespace service;

use service\wechat\MiniApp;
use service\wechat\Official;
use service\wechat\Open;
use service\wechat\Pay;
use service\wechat\PayV3;

/**
 * 微信相关接口
 */
class WeChat
{
    /**
     * 微信支付v2
     * @return  Pay
     */
    public static function pay()
    {
        return Pay::instance();
    }

    /**
     * 微信支付v3
     * @return   PayV3
     */
    public static function payV3()
    {
        return PayV3::instance();
    }

    /**
     * 微信开放平台
     * @return  Official
     */
    public static function official()
    {
        return Official::instance();
    }

    /**
     * 微信小程序
     * @return  MiniApp
     */
    public static function miniapp()
    {
        return MiniApp::instance();
    }

    /**
     * 微信开放平台
     * @return Open
     */
    public static function open()
    {
        return Open::instance();
    }
}