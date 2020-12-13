<?php
/*
 * @Description   支付宝支付基类
 * @Author        lifetime
 * @Date          2020-12-13 21:45:42
 * @LastEditTime  2020-12-13 23:01:15
 * @LastEditors   lifetime
 */

namespace service\ali;

use service\config\AliConfig;
use service\exceptions\InvalidArgumentException;

abstract class Basic
{
    /**
     * 配置
     * @var DataArray
     */
    protected $config;

    /**
     * 当前请求数据
     * @var DataArray
     */
    protected $options;

    /**
     * DzContent数据
     * @var DataArray
     */
    protected $params;

    /**
     * 缓存
     * @var static
     */
    protected static $cache;

    /**
     * 请求网关
     * @var string
     */
    protected $gateway;

    /**
     * 构造函数
     * @param   array   $config     配置信息
     */
    public function __construct($config = [])
    {
        $this->config = new AliConfig($config);

        if (empty($this->config['appid'])) {
            throw new InvalidArgumentException("Miss Config [appid]");
        }
        if (empty($this->config['public_key'])) {
            throw new InvalidArgumentException("Missing Config -- [public_key]");
        }
        if (empty($this->config['private_key'])) {
            throw new InvalidArgumentException("Missing Config -- [private_key]");
        }

        if ($this->config['sandbox']) {
            $this->gateway = 'https://openapi.alipaydev.com/gateway.do';
        } else {
            $this->gateway = 'https://openapi.alipay.com/gateway.do';
        }
    }

    /**
     * 静态创建对象
     * @param   array   $config     配置信息
     */
    public static function instance(array $config = [])
    {
        $key = md5(get_called_class() . serialize($config));
        if (isset(self::$cache[$key])) return self::$cache[$key];
        return self::$cache[$key] = new static($config);
    }
}