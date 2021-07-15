<?php
/*
 * @Description   微信支付
 * @Author        lifetime
 * @Date          2020-12-21 10:04:57
 * @LastEditTime  2021-01-19 13:45:26
 * @LastEditors   lifetime
 */
namespace service\wechat;

use service\tools\BasicBusiness;
use service\wechat\pay\v2\App;
use service\wechat\pay\v2\H5;
use service\wechat\pay\v2\JsApi;
use service\wechat\pay\v2\MiniApp;
use service\wechat\pay\v2\Native;

/**
 * 微信支付
 */
class Pay extends BasicBusiness
{
    /**
     * JSAPi支付
     * @param   array   $config     配置
     * @return  \service\wechat\pay\v2\JsApi
     */
    public function jsApi(array $config = [])
    {
        return JsApi::instance($config);
    }

    /**
     * Native支付
     * @param   array   $config     配置
     * @return  \service\wechat\pay\v2\Native
     */
    public function native(array $config = [])
    {
        return Native::instance($config);
    }

    /**
     * H5支付
     * @param   array   $config     配置
     * @return  \service\wechat\pay\v2\H5
     */
    public function h5(array $config = [])
    {
        return H5::instance($config);
    }

    /**
     * APP支付
     * @param   array   $config     配置
     * @return  \service\wechat\pay\v2\App
     */
    public function app(array $config = [])
    {
        return App::instance($config);
    }

    /**
     * 小程序支付
     * @param   array   $config     配置
     * @return  \service\wechat\pay\v2\MiniApp
     */
    public function miniApp(array $config = [])
    {
        return MiniApp::instance($config);
    }
}
