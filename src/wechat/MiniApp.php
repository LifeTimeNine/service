<?php
/*
 * @Description   微信小程序相关接口
 * @Author        lifetime
 * @Date          2021-01-16 19:46:31
 * @LastEditTime  2021-01-16 21:01:32
 * @LastEditors   lifetime
 */
namespace service\wechat;

use service\wechat\miniapp\Login;
use service\wechat\miniapp\UserInfo;

/**
 * 微信小程序相关接口
 * @class   MiniApp
 */
class MiniApp
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
     * 登录功能
     * @return  Login
     */
    public function login()
    {
        return Login::instance($this->config);
    }

    /**
     * 用户信息功能
     * @return  UserInfo
     */
    public function userInfo()
    {
        return UserInfo::instance($this->config);
    }
}