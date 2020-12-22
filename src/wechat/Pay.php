<?php
/*
 * @Description   微信支付
 * @Author        lifetime
 * @Date          2020-12-21 10:04:57
 * @LastEditTime  2020-12-22 15:46:32
 * @LastEditors   lifetime
 */
namespace service\wechat;

use service\wechat\pay\App;
use service\wechat\pay\H5;
use service\wechat\pay\JsApi;
use service\wechat\pay\MiniApp;
use service\wechat\pay\Native;

class Pay
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
     * JSAPi或小程序支付
     * @return  \service\wechat\pay\JsApi
     */
    public function jsApi()
    {
        return JsApi::instance($this->config);
    }

    /**
     * Native支付
     * @return  \service\wechat\pay\Native
     */
    public function native()
    {
        return Native::instance($this->config);
    }

    /**
     * H5支付
     * @return  \service\wechat\pay\H5
     */
    public function h5()
    {
        return H5::instance($this->config);
    }

    /**
     * APP支付
     * @return  \service\wechat\pay\App
     */
    public function app()
    {
        return App::instance($this->config);
    }

    /**
     * 小程序支付
     * @return  \service\wechat\pay\MiniApp
     */
    public function miniApp()
    {
        return MiniApp::instance($this->config);
    }
}
