<?php

namespace service\byteDance\kernel;

use service\config\ByteDanceConfig;
use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;
use service\tools\Cache;
use service\tools\DataArray;
use service\tools\Tools;

/**
 * 抖店基类
 */
abstract class BasicShakeShop
{
    /**
     * 平台所有配置
     * @var \service\config\ByteDanceConfig
     */
    protected $platformConfig;
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
     * 必须的配置参数
     * @var array
     */
    protected $mustConfig = ['app_key', 'app_secret', 'shop_id'];
    /**
     * 请求公共参数列表
     * @var array
     */
    protected $publicParams = [
        'app_key' => '',
        'method' => '',
        'param_json' => '',
        'timestamp' => '',
        'v' => 2,
    ];
    /**
     * 请求参数列表
     * @var array
     */
    protected $params = [];

    /**
     * 请求地址
     * @var string
     */
    protected $domain = 'https://openapi-fxg.jinritemai.com';

    /**
     * 构造函数
     * @param   array   $config     配置信息
     */
    protected function __construct($config = [])
    {
        $this->platformConfig = new ByteDanceConfig($config);

        $default = $this->platformConfig->get('shakeshop')['default']??null;
        $this->config = new DataArray($this->platformConfig->get('shakeshop')['shops'][$default] ?? []);

        foreach ($this->mustConfig as $v) {
            if (empty($this->config->get($v))) {
                throw new InvalidArgumentException("Missing Config [{$v}]");
            }
        }
        
        $this->publicParams['app_key'] = $this->config->get('app_key');
    }

    /**
     * 切换店铺
     * @access public
     * @param   string  $name   店铺名称
     * @return  $this
     */
    public function shop(string $name)
    {
        $this->config = new DataArray($this->platformConfig->get('shakeshop')['shops'][$name] ?? []);

        foreach ($this->mustConfig as $v) {
            if (empty($this->config->get($v))) {
                throw new InvalidArgumentException("Missing Config [{$v}]");
            }
        }
        
        $this->publicParams['app_key'] = $this->config->get('app_key');

        return $this;
    }

    /**
     * 静态实例化
     * @param   array   $config     配置信息
     * @return $this
     */
    public static function instance(array $config = [])
    {
        $key = md5(get_called_class() . serialize($config));
        if (!isset(self::$instanceList[$key])) self::$instanceList[$key] = new static($config);
        return self::$instanceList[$key];
    }

    /**
     * 设置接口名称
     * @param   string  $funcName   方法名称
     * @param   bool    $autoBuild  是否是自动生成
     * @return $this
     */
    protected function setMethodName($funcName, $autoBuild = true)
    {
        if ($autoBuild) {
            $this->publicParams['method'] = lcfirst(basename(str_replace('\\', '/', get_called_class()))) . ".{$funcName}";
        } else {
            $this->publicParams['method'] = $funcName;
        }
        // 解决单例参数保留问题
        $this->params = [];
        return $this;
    }

    /**
     * 设置请求参数
     * @param   string|array    $name   参数名称|参数列表
     * @param   mixed           $value  参数值
     * @return $this
     */
    protected function setParam($name, $value = null)
    {
        if (is_string($name)) {
            $this->params[$name] = $value;
        } elseif (is_array($name)) {
            $this->params = array_merge($this->params, $name);
        }
        return $this;
    }

    /**
     * 批量可选参数
     * @param   array   $optionals   参数列表
     * @param   array   $allowKeys    允许参数名称列表
     * @return $this
     */
    protected function setOptionals($optionals, $allowKeys)
    {
        $this->setParam($this->filterParam($optionals, $allowKeys));
        return $this;
    }

    /**
     * 过滤参数
     * @param   array   $optionals   参数列表
     * @param   array   $allowKeys    允许参数名称列表
     * @return  array
     */
    protected function filterParam($optionals, $allowKeys)
    {
        $params = [];
        foreach($optionals as $key => $value) {
            if (in_array($key, $allowKeys)) {
                $params[$key] = $value;
            } elseif (array_key_exists($key, $allowKeys)) {
                $params[$key] = $this->filterParam($optionals[$key], $allowKeys[$key]);
            }
        }
        return $params;
    }

    /**
     * 构建签名
     * @param   array   $params
     * @return string
     */
    protected function bulidSign($params = [])
    {
        $this->recKsort($params);
        $this->publicParams['param_json'] = json_encode($params, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        $this->publicParams['timestamp'] = date('Y-m-d H:i:s');
        $signStr = $this->config->get('app_secret');
        foreach($this->publicParams as $k => $v) $signStr .= $k . $v;
        $signStr .= $this->config->get('app_secret');
        $this->publicParams['sign'] = hash_hmac('sha256', $signStr, $this->config->get('app_secret'));
        $this->publicParams['sign_method'] = 'hmac-sha256';
        return $this;
    }

    /**
     * 递归关联数组排序
     */
    protected function recKsort(array &$arr) {
        $kstring = true;
        foreach ($arr as $k => &$v) {
            if (!is_string($k)) {
                $kstring = false;
            }
            if (is_array($v)) {
                $this->recKsort($v);
            }
        }
        if ($kstring) {
            ksort($arr);
        }
    }

    /**
     * 获取Access-Token
     * @return string
     */
    protected function getAccessToken()
    {
        $key = "bytedance_shakeshop_{$this->config->get('app_key')}_{$this->config->get('shop_id')}_access_token";
        if (!empty($data = Cache::get($key))) {
            if ($data['expire'] > time()) {
                return $data['access_token'];
            } else {
                $this->setMethodName('token.refresh', false);
                $params =[
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $data['refresh_token'],
                ];
            }
        } else {
            $this->setMethodName('token.create', false);
            $params = [
                'code' => '',
                'grant_type' => 'authorization_self',
                'shop_id' => $this->config->get('shop_id'),
            ];
        }

        $this->bulidSign($params);
        $url = $this->domain . '/' . str_replace('.', '/', $this->publicParams['method']);
        $res = Tools::request('GET', $url, [
            'query' => $this->publicParams,
        ]);
        $res = json_decode($res, true);
        if ($res['err_no'] <> 0) {
            throw new InvalidResponseException($res['message'], $res['err_no']);
        }
        Cache::set($key, [
            'access_token' => $res['data']['access_token'],
            'expire' => time() + $res['data']['expires_in'],
            'refresh_token' => $res['data']['refresh_token'],
        ]);
        return $res['data']['access_token'];
    }

    /**
     * 发起请求
     */
    protected function request()
    {
        $accessToken = $this->getAccessToken();
        $this->bulidSign($this->params);
        $this->publicParams['access_token'] = $accessToken;
        $url = $this->domain . '/' . str_replace('.', '/', $this->publicParams['method']);
        $res = Tools::request('POST', $url, [
            'query' => $this->publicParams,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'data' => $this->publicParams['param_json'],
        ]);
        $res = json_decode($res, true);
        if ($res['err_no'] <> 0) {
            throw new InvalidResponseException($res['message'], $res['err_no']);
        }
        return $res['data'];
    }
}