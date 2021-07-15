<?php
/*
 * @Description   字节小程序基类
 * @Author        lifetime
 * @Date          2020-12-23 09:46:54
 * @LastEditTime  2020-12-31 08:45:13
 * @LastEditors   lifetime
 */

namespace service\byteDance\kernel;

use service\config\ByteDanceConfig;
use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;
use service\tools\Cache;
use service\tools\Tools;

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
     * 必须的配置参数
     * @var array
     */
    protected $mustConfig = ['miniapp_appid', 'miniapp_secret'];

    /**
     * access_token
     * @var string
     */
    protected $access_token;

    /**
     * 构造函数
     * @param   array   $config     配置信息
     */
    protected function __construct($config = [])
    {
        $this->config = new ByteDanceConfig($config);

        foreach ($this->mustConfig as $v) {
            if (empty($this->config[$v])) {
                throw new InvalidArgumentException("Missing Config [{$v}]");
            }
        }
    }

    /**
     * 静态创建对象
     * @param   array   $config     配置信息
     * @return  static
     */
    public static function instance(array $config = [])
    {
        $key = md5(get_called_class() . serialize($config));
        if (isset(self::$cache[$key])) return self::$cache[$key];
        return self::$cache[$key] = new static($config);
    }

    /**
     * 获取头条平台access_token
     * @return array [access_token, expires_in]
     */
    public function getAccessToken()
    {
        $this->access_token = Cache::get("byteDance_miniApp_access_token_{$this->config['miniapp_appid']}");
        if (!empty($this->access_token)) return $this->access_token;

        try {
            $result = Tools::json2arr(Tools::request('get', 'https://developer.toutiao.com/api/apps/token', [
                'query' => [
                    'appid' => $this->config['miniapp_appid'],
                    'secret' => $this->config['miniapp_secret'],
                    'grant_type' => 'client_credential'
                ],
            ]));
            if (!empty($result['errcode']) && in_array($result['errcode'], [40002])) {
                Cache::del("byteDance_miniApp_access_token_{$this->config['miniapp_appid']}");
                $this->access_token = '';
                return $this->getAccessToken();
            } elseif (!empty($result['errcode']) && $result['errcode'] <> 0) {
                throw new InvalidResponseException($result['errmsg'], $result['errcode'], $result);
            } else {
                Cache::set("byteDance_miniApp_access_token_{$this->config['miniapp_appid']}", $result['access_token'], $result['expires_in']);
                return $this->access_token = $result['access_token'];
            }
        } catch (InvalidResponseException $e) {
            throw new InvalidResponseException($e->getMessage(), $e->getCode(), $e->getRaw());
        }
    }

    /**
     * 计算支付签名
     * @param   array   $options    参与签名数据
     * @param   string  $appSecret  支付secret
     * @return  string
     */
    protected function getPaySign($options, $appSecret)
    {
        ksort($options);
        $data = [];
        foreach ($options as $k => $v) {
            if ($k <> 'sign' && $k <> 'risk_info' && !empty($v)) {
                $data[] = "{$k}={$v}";
            }
        }
        $dataStr = implode('&', $data);
        return md5("{$dataStr}{$appSecret}");
    }
}
