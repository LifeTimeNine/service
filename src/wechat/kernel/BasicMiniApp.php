<?php
/*
 * @Description   微信小程序基类
 * @Author        lifetime
 * @Date          2021-01-15 18:12:47
 * @LastEditTime  2022-03-08 14:59:39
 * @LastEditors   lifetime
 */
namespace service\wechat\kernel;

use service\config\WechatConfig;
use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidRequestException;
use service\exceptions\InvalidResponseException;
use service\tools\Cache;
use service\tools\Tools;

/**
 * 微信小程序基类
 * @class   BasicMiniApp
 */
class BasicMiniApp
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
     * access_token 失效返回的状态码
     * @var array
     */
    protected $failure_code = ['40014', '40001', '41001', '42001'];

    /**
     * 构造函数
     * @param   array   $config     配置信息
     */
    protected function __construct($config = [])
    {
        $this->config = new WechatConfig($config);

        if (empty($this->config['miniapp_appid'])) throw new InvalidArgumentException("Missing Config [miniapp_appid]");

        if (empty($this->config['miniapp_app_secret'])) throw new InvalidArgumentException("Missing Config [miniapp_app_secret]");
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
     * 获取小程序全局唯一后台接口调用凭据
     * @param   boolean     $force      强制获取最新的
     * @return  string
     */
    public function getAccessToken(bool $force = false)
    {
        $accessToken = Cache::get("wechat_miniapp_accesstoken_{$this->config['miniapp_appid']}");
        if (empty($accessToken) || $force) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token';
            $result = Tools::json2arr(Tools::request('GET', $url, [
                'query' => [
                    'appid' => $this->config['miniapp_appid'],
                    'grant_type' => 'client_credential',
                    'secret' => $this->config['miniapp_app_secret'],
                ]
            ]));
            if (!empty($result['access_token']) && !empty($result['expires_in'])) {
                Cache::set("wechat_miniapp_accesstoken_{$this->config['miniapp_appid']}", $result['access_token'], $result['expires_in']);
                return $result['access_token'];
            }
            throw new InvalidResponseException($result['errmsg'], $result['errcode'], $result);
        }
        return $accessToken;
    }

    /**
     * 发起请求(自动完成access_token)
     * @param   string  $url        请求地址
     * @param   string  $method     请求方法
     * @param   array   $options    请求参数
     * @return  array
     */
    protected function request($url, $method = 'GET', $options = [])
    {
        $query = empty($options['query']) ? [] : $options['query'];
        $query['access_token'] = $this->getAccessToken();
        $options['query'] = $query;
        $result = Tools::json2arr(Tools::request($method, $url, $options));
        if (!empty($result['errcode']) || in_array($result['errcode'], $this->failure_code)) {
            $options['query']['access_token'] = $this->getAccessToken(true);
            $result = Tools::json2arr(Tools::request($method, $url, $options));
        }
        if (!empty($result['errcode'])) throw new InvalidRequestException($result['errmsg'], $result['errcode'], $result);
        return $result;
    }
}
