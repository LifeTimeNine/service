<?php
/*
 * @Description   微信支付V3基类
 * @Author        lifetime
 * @Date          2020-12-28 10:33:06
 * @LastEditTime  2021-10-27 16:58:22
 * @LastEditors   lifetime
 */

namespace service\wechat\kernel;

use service\config\WechatConfig;
use service\exceptions\InvalidArgumentException;
use service\tools\Cache;
use service\tools\DataArray;
use service\tools\Tools;

/**
 * 微信支付V3基类
 */
class BasicPayV3
{
    /**
     * 配置
     * @var DataArray
     */
    protected $config;
    /**
     * @var string
     */
    protected $appId;

    /**
     * 缓存
     * @var static
     */
    protected static $cache;

    /**
     * 请求数据
     * @var DataArray
     */
    protected $options;

    /**
     * 订单必须参数
     * @var DataArray
     */
    protected $mustOptions;

    /**
     * 请求接口地址
     * @var string
     */
    protected $apiDomain = 'https://api.mch.weixin.qq.com';

    /**
     * 构造函数
     * @param   array   $config     配置信息
     */
    protected function __construct($config = [])
    {
        $this->config = new WechatConfig($config);
        if (empty($this->config['mch_key'])) throw new InvalidArgumentException('Missinng Config [mch_key]');
        if (empty($this->config['ssl_key']) || !file_exists($this->config['ssl_key'])) throw new InvalidArgumentException('Missing Config [ssl_key]');
        if (empty($this->config['ssl_cert']) || !file_exists($this->config['ssl_cert'])) throw new InvalidArgumentException('Missing Config [ssl_cert]');

        $this->mustOptions = new DataArray(['description', 'out_trade_no', 'amount' => ['total'], 'notify_url']);
    }

    /**
     * 初始化参数
     */
    protected function initOptions()
    {
        $this->options = new DataArray([]);
        if (empty($this->config['mch_id'])) throw new InvalidArgumentException("Missing Config [mch_id]");
        $this->options->set('mchid', $this->config['mch_id']);
        $this->options->set('appid', $this->appId);
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
     * 设置appid
     * @param   string  $key    appid在配置中的key
     */
    protected function setAppId($key)
    {
        if (empty($this->config[$key])) {
            throw new InvalidArgumentException("Missing Config [{$key}]");
        } else {
            $this->appId = $this->config[$key];
        }
    }

    /**
     * 设置场景必须参数
     * @param   array   $data   当前场景必须的参数
     */
    protected function setMustOptions($data)
    {
        $this->mustOptions->merge($data, true);
    }

    /**
     * 验证订单参数是否足够
     * @param   array   $data   要验证的数据
     * @param   array   $field  必须的字段
     * @param   array   $msg    消息
     */
    protected function checkOrder($data = [], $field = [], $msg = [])
    {
        foreach ($field as $k => $v) {
            if (is_string($v) && empty($data[$v])) {
                $msg[] = $v;
                throw new InvalidArgumentException("Missing Options [". implode('.', $msg) ."]");
            } elseif (is_array($v)) {
                if (empty($data[$k])) {
                    $msg[] = $k;
                    throw new InvalidArgumentException("Missing Options [". implode('.', $msg) ."]");
                } else {
                    $this->checkOrder($data[$k], $field[$k], array_merge($msg, [$k]));
                }
            }
        }
    }

    /**
     * 获取API私钥
     * @return  string
     */
    protected function getApiPrivateKey()
    {
        return file_get_contents($this->config['ssl_key']);
    }

    /**
     * 获取证书序列号
     * @return  string
     */
    protected function getSerialNo()
    {
        return openssl_x509_parse(file_get_contents($this->config['ssl_cert']))['serialNumberHex'];
    }

    /**
     * 获取sign
     * @param   array   $data   要签名的数据
     * @param   int     $type   签名类型
     * @return  string
     */
    protected function getSign($data, $type = OPENSSL_ALGO_SHA256)
    {
        $dataStr = '';
        foreach ($data as $v) $dataStr .= "{$v}\n";
        $sign = '';
        if ($type == OPENSSL_ALGO_SHA256) {
            openssl_sign($dataStr, $sign, $this->getApiPrivateKey(), OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($dataStr, $sign, $this->getApiPrivateKey());
        }
        return base64_encode($sign);
    }

    /**
     * 获取Authorization字符串
     * @param   string  $requestMethod     请求方法
     * @param   string  $requestUrl        请求地址
     * @param   string  $requestBody       请求报文主体
     * @return  string
     */
    protected function getAuthoriztionStr($requestMethod, $requestUrl, $requestBody = null)
    {
        $time = time();
        $nonce_str = Tools::createNoncestr();

        $signStr = $this->getSign([$requestMethod, $requestUrl, $time, $nonce_str, $requestBody]);

        $data = [
            'mchid' => $this->config['mch_id'],
            'serial_no' => $this->getSerialNo(),
            'nonce_str' => $nonce_str,
            'timestamp' => $time,
            'signature' => $signStr
        ];
        $data2 = [];
        foreach ($data as $k => $v) $data2[] = "{$k}=\"{$v}\"";

        return 'WECHATPAY2-SHA256-RSA2048 ' . implode(',', $data2);
    }

    /**
     * 发起请求
     * @param   string  $method 请求方法
     * @param   string  $url    请求地址
     * @param   array   $body   请求主体
     * @param   array   $query  请求query参数
     * @return  array
     */
    protected function request($method, $url, $body = null, $query = [])
    {
        if (empty($query)) {
            $signUrl = $url;
        } else {
            $signUrl = "{$url}?" . http_build_query($query);
        }
        if (empty($body)) {
            $body = '';
        } else {
            $body = json_encode($body, 256);
        }
        $result = Tools::request($method, "{$this->apiDomain}{$url}", [
            'headers' => [
                "Authorization: {$this->getAuthoriztionStr($method,$signUrl,$body)}",
                'Content-Type: application/json',
                'Accept: application/json',
                "User-Agent: {$_SERVER['HTTP_USER_AGENT']}"
            ],
            'data' => $body,
            'query' => $query
        ]);
        return Tools::json2arr($result);
    }

    /**
     * 统一下单
     * @param   string  $url        请求url
     * @param   array   $options    订单参数
     * @return  array
     */
    protected function createOrder($url, $options)
    {
        $this->options->merge($options, true);
        $this->checkOrder($this->options->get(), $this->mustOptions->get());
        return $this->request('POST', $url, $this->options->get());
    }

    /**
     * 订单查询
     * @param   array   $options    查询参数[二选其一：transaction_id-微信支付订单号，out_trade_no-商户订单号]
     * @return  array
     */
    public function query(array $options)
    {
        $this->initOptions();
        $url = '';
        if (!empty($options['transaction_id'])) {
            $url = "/v3/pay/transactions/id/{$options['transaction_id']}";
        } elseif (!empty($options['out_trade_no'])) {
            $url = "/v3/pay/transactions/out-trade-no/{$options['out_trade_no']}";
        } else {
            throw new InvalidArgumentException("Missing Options [transaction_id  OR out_trade_no]");
        }

        return $this->request('GET', $url, null, ['mchid' => $this->config['mch_id']]);
    }

    /**
     * 关闭订单
     * @param   string  $out_trade_no   商户订单号
     * @return  array
     */
    public function close(string $out_trade_no)
    {
        $this->initOptions();
        if (empty($out_trade_no)) throw new InvalidArgumentException('Missing Options [out_trade_no]');

        $url = "/v3/pay/transactions/out-trade-no/{$out_trade_no}/close";

        return $this->request('POST', $url, ['mchid' => $this->config['mch_id']]);
    }

    /**
     * 解密AEAD_AES_256_GCM
     * @param   string  $ciphertext     密文
     * @param   string  $nonceStr       随机字符串
     * @param   string  $associatedData 附加数据包
     * @return  string
     */
    protected function decodeAes256Gcm($ciphertext, $nonceStr, $associatedData)
    {
        if (empty($this->config['mch_key_v3'])) throw new InvalidArgumentException("Missing Config [mch_key_v3]");
        
        $ciphertext = base64_decode($ciphertext);
        $ctext = substr($ciphertext, 0, -16);
        $authTag = substr($ciphertext, -16);
        return openssl_decrypt($ctext, 'aes-256-gcm', $this->config['mch_key_v3'], OPENSSL_RAW_DATA, $nonceStr, $authTag, $associatedData);
    }

    /**
     * 获取微信平台证书
     * @return  array
     */
    protected function getCert()
    {
        $wechatPublicCert = Cache::get("wechat_public_cert_{$this->config['mch_id']}");
        if (!empty($wechatPublicCert)) return $wechatPublicCert;

        $url = "/v3/certificates";

        $result = $this->request('GET', $url);
        $cretData = $result['data'][count($result['data']) - 1];
        $wechatPublicCert = $this->decodeAes256Gcm($cretData['encrypt_certificate']['ciphertext'],  $cretData['encrypt_certificate']['nonce'], $cretData['encrypt_certificate']['associated_data']);
        Cache::set("wechat_public_cert_{$this->config['mch_id']}", $wechatPublicCert, 3600 * 11);
        return $wechatPublicCert;
    }

    /**
     * 验证sign
     * @param   string  $data               sign源数据
     * @param   string  $sign               sign
     */
    protected function checkSign($data, $sign, $signType = OPENSSL_ALGO_SHA256)
    {
        if ($signType == OPENSSL_ALGO_SHA256) {
            return openssl_verify($data, base64_decode($sign), $this->getCert(), OPENSSL_ALGO_SHA256) === 1;
        } else {
            return openssl_verify($data, base64_decode($sign), $this->getCert()) === 1;
        }
    }

    /**
     * 异步通知
     * @param   callable    $callable   回调方法（可以接收的参数：$data-解析到的数据）
     * @return  string  给微信返回的消息,如果回调方法返回false则直接给微信返回失败的消息
     */
    public function notify(callable $callable)
    {
        $postData = !empty($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (empty($_SERVER['HTTP_WECHATPAY_TIMESTAMP']) || empty($_SERVER['HTTP_WECHATPAY_NONCE']) || empty($_SERVER['HTTP_WECHATPAY_SIGNATURE'])) {
            return json_encode(['code' => 'FAIL', 'message' => 'Signature verification failed']);
        }
        $checkSign = $this->checkSign("{$_SERVER['HTTP_WECHATPAY_TIMESTAMP']}\n{$_SERVER['HTTP_WECHATPAY_NONCE']}\n{$postData}\n", $_SERVER['HTTP_WECHATPAY_SIGNATURE']);
        if (!$checkSign) {
            $callable([], false);
            return json_encode(['code' => 'FAIL', 'message' => 'Signature verification failed']);
        }
        $postData = json_decode($postData, true);
        $data = $this->decodeAes256Gcm($postData['resource']['ciphertext'], $postData['resource']['nonce'], $postData['resource']['associated_data']);
        $result = $callable(json_decode($data, true), true);
        if ($result !== false) {
            return json_encode(['code' => 'SUCCESS', 'message' => 'success']);
        } else {
            return json_encode(['code' => 'FAIL', 'message' => 'Business failure']);
        }
        
    }
    
}
