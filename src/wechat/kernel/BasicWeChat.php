<?php
/*
 * @Description   微信公众平台相关接口
 * @Author        lifetime
 * @Date          2020-12-18 21:26:38
 * @LastEditTime  2020-12-23 18:30:27
 * @LastEditors   lifetime
 */

namespace service\wechat\kernel;

use service\config\WechatConfig;
use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;
use service\tools\Cache;
use service\tools\Tools;

class BasicWeChat
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
     * access_token 失效返回的状态码
     * @var array
     */
    protected $failure_code = ['40014', '40001', '41001', '42001'];

    /**
     * 构造函数
     * @param   array   $config     配置参数
     */
    protected function __construct($config = [])
    {
        $this->config = new WechatConfig($config);

        if (empty($this->config['official_appid'])) throw new InvalidArgumentException("Missing Config [official_appid]");

        if (empty($this->config['official_app_secret'])) throw new InvalidArgumentException("Missing Config [official_app_secret]");
    }

    /**
     * 静态创建对象
     * @param   array   $config     配置参数
     * @return  static
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
        $this->access_token = Cache::get("wechat_access_token_{$this->config['appid']}");

        if (!empty($this->access_token)) return $this->access_token;

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->config['official_appid']}&secret={$this->config['official_app_secret']}";
        $res = json_decode(Tools::request('get', $url), true);
        if (!empty($res['errcode'])) throw new InvalidResponseException("errcode: [{$res['errcode']}]  errmsg: [{$res['errmsg']}]");

        Cache::set("wechat_access_token_{$this->config['appid']}", $res['access_token'], $res['expires_in']);

        return $this->access_token = $res['access_token'];
    }

    /**
     * 设置access_token
     * @param   string  $access_token   access_token
     * @param   int     $exp           有效期
     */
    public function setAccessToken(string $access_token, $exp)
    {
        Cache::set("wechat_access_token_{$this->config['appid']}", $this->access_token = $access_token, $exp);
    }

    /**
     * 删除access_token
     */
    public function delAccessToken()
    {
        $this->access_token = '';
        Cache::del("wechat_access_token_{$this->config['appid']}");
    }

    /**
     * 注册请求
     * @param   string  $url    接口地址
     * @param   string  $method 当前方法名
     * @param   array   $arguments  当前方法参数
     */
    protected function registerHttp($url, $method, $arguments)
    {
        $this->currentMethod = ['method' => $method, 'arguments' => $arguments];
        if (empty($this->access_token)) $this->access_token = $this->getAccessToken();
        
        $this->request_url = str_replace('ACCESS_TOKEN', urlencode($this->access_token), $url);
    }

    /**
     * 以get发起http请求并将结果转换为数组
     * @return  array
     */
    protected function httpGetForJson()
    {
        try {
            $result = Tools::json2arr(Tools::request('get', $this->request_url));
            if (!empty($result['errcode']) && $result['errcode'] !== 0) {
                throw new InvalidResponseException($result['return_msg'], '0', $result);
            }
            return $result;
        } catch (InvalidResponseException $e) {
            if (in_array($e->getCode(), $this->failure_code)) {
                return call_user_func_array([$this, $this->currentMethod['method']], $this->currentMethod['arguments']);
            }
            throw new InvalidResponseException($e->getMessage(), $e->getCode(), $e->getRaw());
        }
    }


    /**
     * 以post发起http请求并将结果转换为数组
     * @param   array   $data   请求数据
     * @param   bool    $buildToJson    是否转换为sson
     * @return  array
     */
    protected function httpPostForJson($data, $buildToJson = true)
    {
        $options = [
            'headers' => ['Content-Type: application/json'],
            'data' => $buildToJson ? Tools::arr2json($data) : $data,
        ];

        try {
            $result = Tools::json2arr(Tools::request('post', $this->request_url, $options));
            if (!empty($result['errcode']) && $result['errcode'] !== 0) {
                throw new InvalidResponseException($result['return_msg'], '0', $result);
            }
            return $result;
        } catch (InvalidResponseException $e) {
            if (in_array($e->getCode(), $this->failure_code)) {
                return call_user_func_array([$this, $this->currentMethod['method']], $this->currentMethod['arguments']);
            }
            throw new InvalidResponseException($e->getMessage(), $e->getCode(), $e->getRaw());
        }
    }
}