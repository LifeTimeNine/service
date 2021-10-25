<?php
/*
 * @Description   字节跳动相关接口
 * @Author        lifetime
 * @Date          2021-01-15 17:13:20
 * @LastEditTime  2021-10-23 16:23:11
 * @LastEditors   lifetime
 */
namespace service;

use service\byteDance\MiniApp;
use service\byteDance\ShakeShop;

/**
 * 字节跳动相关接口
 * @class ByteDance
 */
class ByteDance
{
    /**
     * 字节小程序
     * @param   array   $config
     * @return MiniApp
     */
    public static function miniApp(array $config = [])
    {
        return MiniApp::instance($config);
    }

    /**
     * 抖店
     * @return \service\byteDance\ShakeShop
     */
    public static function shakeShop()
    {
        return ShakeShop::instance();
    }
}
