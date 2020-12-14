<?php
/*
 * @Description   支付宝支付基类
 * @Author        lifetime
 * @Date          2020-12-13 21:45:42
 * @LastEditTime  2020-12-14 22:25:13
 * @LastEditors   lifetime
 */

namespace service\ali;

use service\config\AliConfig;
use service\exceptions\InvalidArgumentException;
use WeChat\Contracts\DataArray;

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
     * bizContent数据
     * @var DataArray
     */
    protected $bizContent;

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
        $this->applyOptions();
        $this->bizContent = new DataArray([]);
    }

    /**
     * 整理请求公共参数
     */
    protected function applyOptions()
    {
        $this->options = new DataArray([
            'app_id' => $this->config['appid'],
            'version' => $this->config['version'],
            'format' => $this->config['format'],
            'sign_type' => $this->config['sign_type'],
            'charset' => $this->config['charset']
        ]);
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

    /**
     * 获取数据签名
     * @return string
     */
    protected function getSign()
    {
        $content = wordwrap($this->trimCert($this->config->get('private_key')), 64, "\n", true);
        $string = "-----BEGIN RSA PRIVATE KEY-----\n{$content}\n-----END RSA PRIVATE KEY-----";
        if ($this->options->get('sign_type') === 'RSA2') {
            openssl_sign($this->getSignContent($this->options->get(), true), $sign, $string, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($this->getSignContent($this->options->get(), true), $sign, $string, OPENSSL_ALGO_SHA1);
        }
        return base64_encode($sign);
    }

    /**
     * 去除证书前后内容及空白
     * @param string $sign
     * @return string
     */
    protected function trimCert($sign)
    {
        // if (file_exists($sign)) $sign = file_get_contents($sign);
        return preg_replace(['/\s+/', '/\-{5}.*?\-{5}/'], '', $sign);
    }

    /**
     * 数据签名处理
     * @param array $data 需要进行签名数据
     * @param boolean $needSignType 是否需要sign_type字段
     * @return bool|string
     */
    private function getSignContent(array $data, $needSignType = false)
    {
        list($attrs,) = [[], ksort($data)];
        if (isset($data['sign'])) unset($data['sign']);
        if (empty($needSignType)) unset($data['sign_type']);
        foreach ($data as $key => $value) {
            if ($value === '' || is_null($value)) continue;
            array_push($attrs, "{$key}={$value}");
        }
        return join('&', $attrs);
    }
}