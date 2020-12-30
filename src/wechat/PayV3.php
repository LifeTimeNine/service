<?php
/*
 * @Description   微信支付 V3.0
 * @Author        lifetime
 * @Date          2020-12-28 21:48:10
 * @LastEditTime  2020-12-30 10:52:10
 * @LastEditors   lifetime
 */
namespace service\wechat;

use service\wechat\pay\v3\App;
use service\wechat\pay\v3\H5;
use service\wechat\pay\v3\JsApi;
use service\wechat\pay\v3\MiniApp;
use service\wechat\pay\v3\Native;

/**
 * 微信支付 V3.0
 */
class PayV3
{
    /**
     * 配置
     * @var array
     */
    protected $config;
    /**
     * 缓存
     * @var static
     */
    protected static $cache;

    /**
     * 构造函数
     * @param   array   $config     配置参数
     */
    protected function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * 静态创建对象
     * @param   array   $config     配置信息
     * @return  $this
     */
    public static function instance(array $config = [])
    {
        $key = md5(get_called_class() . serialize($config));
        if (isset(self::$cache[$key])) return self::$cache[$key];
        return self::$cache[$key] = new static($config);
    }

    /**
     * JSAPi支付
     * @return  \service\wechat\pay\v3\JsApi
     */
    public function jsApi()
    {
        return JsApi::instance($this->config);
    }

    /**
     * Native支付
     * @return  \service\wechat\pay\v3\Native
     */
    public function native()
    {
        return Native::instance($this->config);
    }

    /**
     * H5支付
     * @return  \service\wechat\pay\v3\H5
     */
    public function h5()
    {
        return H5::instance($this->config);
    }

    /**
     * APP支付
     * @return  \service\wechat\pay\v3\App
     */
    public function app()
    {
        return App::instance($this->config);
    }

    /**
     * 小程序支付
     * @return  \service\wechat\pay\v3\MiniApp
     */
    public function miniApp()
    {
        return MiniApp::instance($this->config);
    }
}