<?php
/*
 * @Description   字节小程序基类
 * @Author        lifetime
 * @Date          2020-12-23 09:46:54
 * @LastEditTime  2020-12-23 12:29:59
 * @LastEditors   lifetime
 */
namespace service\byteDance\kernel;

use service\config\ByteDanceConfig;
use service\exceptions\InvalidArgumentException;
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
        if (Cache::get('toutiao_access_token')) return Cache::get('toutiao_access_token');
        $requestData = Tools::request('get', 'https://developer.toutiao.com/api/apps/token', [
            'query' => [
                'appid' => $this->config['appid'],
                'secret' => $this->config['secret'],
                'grant_type' => 'client_credential'
            ],
        ]);
        return Tools::json2arr($requestData);
    }
}