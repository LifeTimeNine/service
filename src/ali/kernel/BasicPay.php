<?php
/*
 * @Description   支付宝支付基类
 * @Author        lifetime
 * @Date          2020-12-13 21:45:42
 * @LastEditTime  2021-01-17 17:33:57
 * @LastEditors   lifetime
 */

namespace service\ali\kernel;

use service\config\AliConfig;
use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;
use service\tools\DataArray;
use service\tools\Tools;

abstract class BasicPay
{
    /**
     * 配置
     * @var DataArray
     */
    protected $config;

    /**
     * 必须的配置参数
     * @var array
     */
    protected $mustConfig = ['appid', 'alipay_public_key', 'private_key'];

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
     * 订单必须参数
     * @var array
     */
    protected $orderMustOptions = ['out_trade_no', 'total_amount', 'subject'];

    /**
     * 构造函数
     * @param   array   $config     配置信息
     */
    protected function __construct($config = [])
    {
        $this->config = new AliConfig($config);

        foreach ($this->mustConfig as $v) {
            if (empty($this->config[$v])) {
                throw new InvalidArgumentException("Missing Config [{$v}]");
            }
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
    private function applyOptions()
    {
        $this->options = new DataArray([
            'app_id' => $this->config['appid'],
            'version' => $this->config['version'],
            'format' => $this->config['format'],
            'sign_type' => $this->config['sign_type'],
            'charset' => $this->config['charset'],
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
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
    protected function getSignContent(array $data, $needSignType = false)
    {
        list($attrs,) = [[], ksort($data)];
        if (isset($data['sign'])) unset($data['sign']);
        if (empty($needSignType)) unset($data['sign_type']);
        foreach ($data as $key => $value) {
            if ($value === '' || is_null($value)) continue;
            array_push($attrs, "{$key}={$value}");
        }
        return implode('&', $attrs);
    }

    /**
     * 验证签名
     * @param   array   $data   阿里返回数据
     * @return  bool    验证结果
     */
    protected function verify($data)
    {
        if (empty($data)) return false;

        if (empty($data['sign']) || empty('sing_type')) return false;
        $sign = $data['sign'];
        $signType = $data['sign_type'];
        $signData = $this->getSignContent($data);

        return $this->checkSign($signData, $sign, $signType);
    }

    /**
     * 生成支付HTML代码
     * @param   array   $order  订单参数
     * @return string
     */
    protected function buildPayHtml($order)
    {
        $this->options->set('biz_content', json_encode($this->bizContent->merge($order, true), 256));
        $this->options->set('sign', $this->getSign());

        $html = "<form id='alipaysubmit' name='alipaysubmit' action='{$this->gateway}' method='post'>";
        foreach ($this->options->get() as $key => $value) {
            $value = str_replace("'", '&apos;', $value);
            $html .= "<input type='hidden' name='{$key}' value='{$value}'/>";
        }
        $html .= "<input type='submit' value='ok' style='display:none;'></form>";
        return "{$html}<script>document.forms['alipaysubmit'].submit();</script>";
    }

    /**
     * 验证sign
     * @param   string  $data               sign源数据
     * @param   string  $sign               sign
     * @param   string  $signType           sign类型
     */
    protected function checkSign($data, $sign, $signType = 'RSA')
    {
        $alipayPublicKey = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($this->config['alipay_public_key'], 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        if ($signType == "RSA2") {
            return openssl_verify($data, base64_decode($sign), $alipayPublicKey, OPENSSL_ALGO_SHA256) === 1;
        } else {
            return openssl_verify($data, base64_decode($sign), $alipayPublicKey) === 1;
        }
    }

    /**
     * 验证订单必须参数
     * @param   array   $data   订单数据
     * @return  mexid   验证结果
     */
    protected function checkOrder($data)
    {
        if (!is_array($data)) throw new InvalidArgumentException("Missing Options type");

        foreach ($this->orderMustOptions as $v) {
            if (empty($data[$v])) throw new InvalidArgumentException("Miss Options [{$v}]");
        }
        return true;
    }

    /**
     * 请求支付宝
     * @param  array    $options    请求参数
     * @return array    [相应数据， 验证签名结果]
     */
    protected function requestAli($options)
    {
        $this->options->set('biz_content', json_encode($this->bizContent->merge($options, true), 256));
        $this->options->set('sign', $this->getSign());

        try {
            $res = Tools::request('get', $this->gateway, [
                'query' => $this->options->get()
            ]);
        } catch (\Exception $e) {
            throw new InvalidResponseException($e->getMessage(), $e->getCode());
        }

        $resData = json_decode($res, true);
        if (json_last_error() != JSON_ERROR_NONE) throw new InvalidResponseException("The request result resolution failed");

        return $this->checkResponse($resData);
    }

    /**
     * 验证返回数据的签名
     * @param   array   $resData    返回数据
     * @return  bool
     */
    protected function checkResponse($resData)
    {
        if (empty($resData['sign'])) throw new InvalidResponseException("Missing Response data");
        $sign = $resData['sign'];

        $methodName = str_replace('.', '_', $this->options['method']) . '_response';

        if (empty($resData[$methodName])) throw new InvalidResponseException("Missing Response data");
        $data = $resData[$methodName];

        return [$data, $this->checkSign(json_encode($data, 256), $sign, $this->options['sign_type'])];
    }
}
