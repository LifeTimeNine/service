<?php
/*
 * @Description   字节小程序基类
 * @Author        lifetime
 * @Date          2020-12-23 09:46:54
 * @LastEditTime  2020-12-23 18:09:32
 * @LastEditors   lifetime
 */
namespace service\byteDance\kernel;

use service\config\ByteDanceConfig;
use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidRequestException;
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
    protected $mustConfig = ['miniapp_appid', 'miniapp_secret', 'cache_path'];

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
        $requestData = Tools::request('get', 'https://developer.toutiao.com/api/apps/token', [
            'query' => [
                'appid' => $this->config['appid'],
                'secret' => $this->config['secret'],
                'grant_type' => 'client_credential'
            ],
        ]);
        $result = Tools::json2arr($requestData);
        if (in_array($result['errcode'], [40002])) {
            Cache::del("byteDance_miniApp_access_token_{$this->config['miniapp_appid']}");
        }

        try {
            $result = Tools::json2arr(Tools::request('get', 'https://developer.toutiao.com/api/apps/token', [
                'query' => [
                    'appid' => $this->config['appid'],
                    'secret' => $this->config['secret'],
                    'grant_type' => 'client_credential'
                ],
            ]));
            if (!empty($result['errcode']) && in_array($result['errcode'], [40002])) {
                Cache::del("byteDance_miniApp_access_token_{$this->config['miniapp_appid']}");
                $this->access_token = '';
                return $this->getAccessToken();
            } elseif (!empty($result['errcode']) && $result['errcode'] <> 0) {
                throw new InvalidRequestException($result['errmsg'], $result['errcode'], $result);
            } else {
                Cache::set("byteDance_miniApp_access_token_{$this->config['miniapp_appid']}", $result['access_token'], $result['expires_in']);
            }
        } catch (InvalidRequestException $e) {
            throw new InvalidRequestException($e->getMessage(), $e->getCode(), $e->getRaw());
        }
        return $result;
    }
}