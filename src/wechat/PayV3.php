<?php
/*
 * @Description   微信支付 V3.0
 * @Author        lifetime
 * @Date          2020-12-28 21:48:10
 * @LastEditTime  2021-01-19 13:46:21
 * @LastEditors   lifetime
 */
namespace service\wechat;

use service\tools\BasicBusiness;
use service\wechat\pay\v3\App;
use service\wechat\pay\v3\H5;
use service\wechat\pay\v3\JsApi;
use service\wechat\pay\v3\MiniApp;
use service\wechat\pay\v3\Native;

/**
 * 微信支付 V3.0
 */
class PayV3 extends BasicBusiness
{
    /**
     * JSAPi支付
     * @param   array   $config     配置
     * @return  \service\wechat\pay\v3\JsApi
     */
    public function jsApi(array $config = [])
    {
        return JsApi::instance($config);
    }

    /**
     * Native支付
     * @param   array   $config     配置
     * @return  \service\wechat\pay\v3\Native
     */
    public function native(array $config = [])
    {
        return Native::instance($config);
    }

    /**
     * H5支付
     * @param   array   $config     配置
     * @return  \service\wechat\pay\v3\H5
     */
    public function h5(array $config = [])
    {
        return H5::instance($config);
    }

    /**
     * APP支付
     * @param   array   $config     配置
     * @return  \service\wechat\pay\v3\App
     */
    public function app(array $config = [])
    {
        return App::instance($config);
    }

    /**
     * 小程序支付
     * @param   array   $config     配置
     * @return  \service\wechat\pay\v3\MiniApp
     */
    public function miniApp(array $config = [])
    {
        return MiniApp::instance($config);
    }
}