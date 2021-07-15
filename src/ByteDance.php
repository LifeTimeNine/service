<?php
/*
 * @Description   字节跳动相关接口
 * @Author        lifetime
 * @Date          2021-01-15 17:13:20
 * @LastEditTime  2021-01-15 17:42:35
 * @LastEditors   lifetime
 */
namespace service;

use service\byteDance\MiniApp;

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
}
