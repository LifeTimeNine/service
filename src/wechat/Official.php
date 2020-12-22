<?php
/*
 * @Description   微信公众平台相关接口
 * @Author        lifetime
 * @Date          2020-12-22 08:51:49
 * @LastEditTime  2020-12-22 09:04:31
 * @LastEditors   lifetime
 */
namespace service\wechat;

use service\wechat\official\Oauth;
use service\wechat\official\Template;

class Official
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
     * 网页授权
     * @return  \service\wechat\official\Oauth
     */
    public function oauth()
    {
        return Oauth::instance($this->config);
    }

    /**
     * 模板消息
     * @return \service\wechat\official\Template
     */
    public function template()
    {
        return Template::instance($this->config);
    }
}
