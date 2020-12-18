<?php
/*
 * @Description   微信公众平台相关接口
 * @Author        lifetime
 * @Date          2020-12-18 21:26:38
 * @LastEditTime  2020-12-18 22:48:29
 * @LastEditors   lifetime
 */

namespace service\wechat;

use service\config\WechatConfig;
use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;
use service\wechat\kernel\Tools;

class Official
{
    /**
     * 配置
     * @var DataArray
     */
    protected $config;

    /**
     * 缓存
     * @var static
     */
    protected static $cache;

    /**
     * access_token
     * @var string
     */
    protected $access_token;

    /**
     * 当前请求方法
     * @var array
     */
    protected $currentMethod = [];

    /**
     * 请求的接口地址
     * @var string
     */
    protected $request_url = '';

    /**
     * 构造函数
     * @param   array   $config     配置参数
     */
    protected function __construct($config = [])
    {
        $this->config = new WechatConfig($config);

        if (empty($this->config['official_appid'])) throw new InvalidArgumentException("Missing Config [official_appid]");

        if (empty($this->config['official_app_secret'])) throw new InvalidArgumentException("Missing Config [official_app_secret]");

        if (empty($this->config['cache_path'])) throw new InvalidArgumentException("Missing Config [cache_path]");

        Tools::$cache_path = $this->config['cache_path'];
    }

    /**
     * 静态创建对象
     * @param   array   $config     配置参数
     * @return  \service\wechat\Official
     */
    public static function instance(array $config = [])
    {
        $key = md5(get_called_class() . serialize($config));
        if (isset(self::$cache[$key])) return self::$cache[$key];
        return self::$cache[$key] = new static($config);
    }

    /**
     * 获取access_token
     * @return  string
     */
    public function getAccessToken()
    {
        $this->access_token = Tools::getCache($this->config['appid'] . '_access_token');

        if (!empty($this->access_token)) return $this->access_token;

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->config['official_appid']}&secret={$this->config['official_app_secret']}";
        $res = json_decode(Tools::request('get', $url), true);
        if (!empty($res['errcode'])) throw new InvalidResponseException("errcode: [{$res['errcode']}]  errmsg: [{$res['errmsg']}]");

        Tools::setCache($this->config['appid'] . '_access_token', $res['access_token'], $res['expires_in']);

        return $this->access_token = $res['access_token'];
    }

    /**
     * 设置access_token
     * @param   string  $access_token   access_token
     */
    public function setAccessToken(string $access_token)
    {
        Tools::setCache("{$this->config['appid']}_access_token", $this->access_token = $access_token);
    }

    /**
     * 删除access_token
     */
    public function delAccessToken()
    {
        $this->access_token = '';
        Tools::delCache("{$this->access_token['appid']}_access_token");
    }

    /**
     * 注册请求
     * @param   string  $url    接口地址
     * @param   string  $method 当前方法名
     * @param   array   $arguments  当前方法参数
     */
    public function registerHttp($url, $method, $arguments)
    {
        
    }

    /**
     * 以post发起http请求并将结果转换为数组
     * 
     */
}