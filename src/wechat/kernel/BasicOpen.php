<?php
/*
 * @Description   开放平台网站应用基类
 * @Author        lifetime
 * @Date          2021-09-27 18:09:17
 * @LastEditTime  2021-09-28 09:18:54
 * @LastEditors   lifetime
 */
namespace service\wechat\kernel;

use service\config\WechatConfig;
use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;
use service\tools\Tools;

/**
 * 开放平台网站应用基类
 * @class BasicOpenWeb
 */
abstract class BasicOpen
{
    /**
     * 配置
     * @var \service\tools\DataArray
     */
    protected $config;
    /**
     * 实例列表
     * @var array
     */
    protected static $instanceList = [];
    /**
     * 当前请求方法
     * @var array
     */
    protected $currentMethod = [];
    /**
     * 请求的接口地址
     * @var string
     */
    protected $requestUrl = '';
    /**
     * access_token 失效返回的状态码
     * @var array
     */
    protected $failure_code = ['40014', '40001', '41001', '42001'];
    /**
     * 应用名称
     * @var string
     */
    protected $app = '';
    /**
     * access_token
     * @var string
     */
    protected $access_token;

    /**
     * 构造函数
     * @param   array   $config     配置参数
     */
    protected function __construct($config = [])
    {
        $this->config = new WechatConfig($config);
        $this->app = $this->config->get('open_webs')['default'];
    }

    /**
     * 静态创建对象
     * @param   array   $config     配置参数
     * @return  static
     */
    public static function instance(array $config = [])
    {
        $key = md5(get_called_class() . serialize($config));
        if (isset(self::$instanceList[$key])) return self::$instanceList[$key];
        return self::$instanceList[$key] = new static($config);
    }

    /**
     * 切换应用
     * @param   string  $appName    应用名称
     * @return $this
     */
    public function app(string $appName)
    {
        $this->app = $appName;
        return $this;
    }

    /**
     * 获取配置
     * @return array
     */
    protected function getConfig()
    {
        // 验证配置信息
        if (empty($this->config->get('open_webs')['apps'][$this->app])) {
            throw new InvalidArgumentException("Missing apps [{$this->app}]");
        }
        if (empty($this->config->get('open_webs')['apps'][$this->app]['appid'])) {
            throw new InvalidArgumentException("Missing apps {$this->app} [appid]");
        }
        if (empty($this->config->get('open_webs')['apps'][$this->app]['app_secret'])) {
            throw new InvalidArgumentException("Missing apps {$this->app} [app_secret]");
        }
        // 返回配置
        return [
            'appid' => $this->config->get('open_webs')['apps'][$this->app]['appid'],
            'app_secret' => $this->config->get('open_webs')['apps'][$this->app]['app_secret'],
        ];
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
        
        $this->requestUrl = str_replace('ACCESS_TOKEN', urlencode($this->access_token), $url);
    }

    /**
     * 以get发起http请求并将结果转换为数组
     * @param   array   $query      请求数据
     * @return  array
     */
    protected function httpGetForJson($query = [])
    {
        try {
            $result = Tools::json2arr(Tools::request('get', $this->requestUrl, ['query' => $query]));
            if (!empty($result['errcode']) && $result['errcode'] !== 0) {
                throw new InvalidResponseException($result['errmsg'], '0', $result);
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
            $result = Tools::json2arr(Tools::request('post', $this->requestUrl, $options));
            if (!empty($result['errcode']) && $result['errcode'] !== 0) {
                throw new InvalidResponseException($result['errmsg'], '0', $result);
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