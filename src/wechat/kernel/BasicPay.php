<?php
/*
 * @Description: 微信支付类
 * @Author: Lifetime
 * @Date: 2020-12-20 11:50:10
 * @LastEditors   lifetime
 * @LastEditTime  2020-12-22 09:20:48
 */

namespace service\wechat\kernel;

use service\DataArray;
use service\config\WechatConfig;
use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;

class BasicPay
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
     * 请求数据
     * @var DataArray
     */
    protected $options;

    /**
     * 订单必须参数
     * @var array
     */
    protected $mustOptions;
    
    /**
     * 构造函数
     * @param   array   $config     配置信息
     */
    protected function __construct($config = [])
    {
        $this->config = new WechatConfig($config);

        $this->options = new DataArray([]);

        if (empty($this->config['mch_id'])) throw new InvalidArgumentException("Missing Config [mch_id]");
        $this->options->set('mch_id', $this->config['mch_id']);
    
        if (empty($this->config['mch_key'])) throw new InvalidArgumentException('Missinng Config [mch_key]');

        if (empty($this->config['cache_path'])) throw new InvalidArgumentException("Missing Config [cache_path]");
        Tools::$cache_path = $this->config['cache_path'];

        if (empty($this->config['sign_type'])) throw new InvalidArgumentException("Missing Config [sign_type]");
        $this->options->set('sign_type', $this->config['sign_type']);
        
        $this->options->set('nonce_str', Tools::createNoncestr());

        $this->mustOptions = new DataArray(['out_trade_no', 'total_fee', 'body', 'notify_url']);
        
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
     * 验证订单参数是否足够
     */
    protected function checkOrder()
    {
        foreach($this->mustOptions->get() as $v)
        {
            if (empty($this->options[$v])) {
                throw new InvalidArgumentException("Missing Options [{$v}]");
            }
        }
    }

    /**
     * 以Post请求接口
     * @param string $url 请求
     * @param array $data  请求数据
     * @param bool $isCert 是否需要使用双向证书
     * @param bool $needSignType 是否需要传签名类型参数
     * @return array
     */
    protected function postApi($url, $data, $isCert = false, $needSignType = true)
    {
        $option = [];
        if ($isCert) {
            
            if (empty($this->config['ssl_cer']) || !file_exists($this->config['ssl_cer'])) {
                throw new InvalidArgumentException("Missing Config [ssl_cer]");
            }
            if (empty($this->config['ssl_key']) || !file_exists($this->config['ssl_key'])) {
                throw new InvalidArgumentException("Missing Config [ssl_key]");
            }

            $option['ssl_cer'] = $this->config->get('ssl_cer');
            $option['ssl_key'] = $this->config->get('ssl_key');
        }

        $signType = $data['sign_type'];
        
        if ($needSignType) {
            $data['sign_type'] = strtoupper($data['sign_type']);
        } else {
            unset($data['sign_type']);
        }

        $data['sign'] = $this->getSign($data, $signType);

        $option['data'] = Tools::arr2xml($data);
        
        $result = Tools::xml2arr(Tools::request('post', $url, $option));
        if ($result['return_code'] !== 'SUCCESS') {
            throw new InvalidResponseException($result['return_msg'], '0');
        }
        return $result;
    }

    /**
     * 生成支付签名
     * @param array $data 参与签名的数据
     * @param string $signType 参与签名的类型
     * @param string $buff 参与签名字符串前缀
     * @return string
     */
    protected function getSign(array $data, $signType = 'MD5', $buff = '')
    {
        ksort($data);
        if (isset($data['sign'])) unset($data['sign']);
        foreach ($data as $k => $v) $buff .= "{$k}={$v}&";
        $buff .= ("key=" . $this->config->get('mch_key'));
        if (strtoupper($signType) === 'MD5') {
            return strtoupper(md5($buff));
        }
        return strtoupper(hash_hmac('SHA256', $buff, $this->config->get('mch_key')));
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
            $this->options->set('appid', $this->config[$key]);
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
     * 统一下单
     * @param   string  $url        统一下单请求地址
     * @param   array   $options    订单参数
     * @return  array
     */
    protected function createOrder($url, $options)
    {
        $this->options->set('spbill_create_ip', $_SERVER['REMOTE_ADDR']);
        
        $this->options->merge($options, true);

        $this->checkOrder();

        return $this->postApi($url, $this->options->get());
    }

    /**
     * 查询订单
     * @param   array   $options    参数 二选一：[transaction_id-微信订单号,out_trade_no-商户订单号]
     * @return array
     */
    public function query(array $options)
    {
        if (empty($options['transaction_id']) && empty($options['out_trade_no'])) {
            throw new InvalidArgumentException("Missing Options [transaction_id  OR out_trade_no]");
        }

        $this->options->merge($options, true);

        $url = "https://api.mch.weixin.qq.com/pay/orderquery";

        $res = $this->postApi($url, $this->options->get());

        return $res;
    }

     /**
     * 退款申请
     * @param   array   $options 参数 [transaction_id-微信订单号,out_trade_no-商户订单号,out_refund_no-商户退款单号,total_fee-订单金额,refund_fee-退款金额]
     * @return array
     */
    public function refund(array $options)
    {
        if (empty($options['transaction_id']) && empty($options['out_trade_no'])) {
            throw new InvalidArgumentException("Missing Options [transaction_id  OR out_trade_no]");
        }
        if (empty($options['out_refund_no'])) {
            throw new InvalidArgumentException('Missing Options [out_refund_no]');
        }
        if (empty($options['total_fee'])) {
            throw new InvalidArgumentException('Missing Options [total_fee]');
        }
        if (empty($options['refund_fee'])) {
            throw new InvalidArgumentException('Missing Options [refund_fee]');
        }

        $this->options->merge($options, true);

        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";

        $res = $this->postApi($url, $this->options->get(), true);

        return $res;
    }

    /**
     * 退款查询
     * @param   array   $options    参数 四选一：[transaction_id-微信订单号,out_trade_no-商户订单号,out_refund_no-商户退款单号,refund_id-微信退款单号]
     * @param   array
     */
    public function refundQuery(array $options)
    {
        if (empty($options['transaction_id']) && empty($options['out_trade_no']) && empty($options['out_refund_no']) && empty($options['refund_id'])) {
            throw new InvalidArgumentException("Missing Options [transaction_id  OR out_trade_no OR out_refund_no OR refund_id]");
        }

        $this->options->merge($options, true);

        $url = "https://api.mch.weixin.qq.com/pay/refundquery";

        $res = $this->postApi($url, $this->options->get());

        return $res;
    }

    /**
     * 异步通知
     * @param   callback    $callback   回调方法，两个参数(data-通知的数据, $checkRes-验签结果)
     * @return  string  返回给微信的XML数据
     */
    public function notify(callable $callback)
    {
        // 获取通知的数据
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (empty($xml)) {
            $callback([], false);
            return Tools::arr2xml([
                'return_code' => 'FAIL',
                'return_msg' => 'Parameter format validation error'
            ]);
        }

        $data = Tools::xml2arr($xml);

        if (empty($data['sign_type']) || empty($data['sign'])) {
            $callback([], false);
            return Tools::arr2xml([
                'return_code' => 'FAIL',
                'return_msg' => 'Parameter format validation error'
            ]);
        };

        $signType = $data['sign_type'];

        $sign = $this->getSign($data, $signType);

        if ($sign == $data['sign']) {
            $callback($data, true);
            return Tools::arr2xml([
                'return_code' => 'SUCCESS',
                'return_msg' => 'OK'
            ]);
        } else {
            $callback($data, false);
            return Tools::arr2xml([
                'return_code' => 'FAIL',
                'return_msg' => 'Signature failure'
            ]);
        }
    }
}