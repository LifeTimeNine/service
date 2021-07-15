<?php
/*
 * @Description   微信 APP支付 V3.0
 * @Author        lifetime
 * @Date          2020-12-22 15:23:40
 * @LastEditTime  2020-12-29 17:42:07
 * @LastEditors   lifetime
 */
namespace service\wechat\pay\v3;

use service\tools\Tools;
use service\wechat\kernel\BasicPayV3;

class App extends BasicPayV3
{
    /**
     * 构造函数
     * @param   array   $config     配置参数
     */
    protected function __construct($config = [])
    {
        parent::__construct($config);
        $this->setAppId('app_appid');
    }

    /**
     * 下单支付
     * @param   array   $options    订单参数 [out_trade_no-订单参数]
     * @param   array   $notify_url 通知地址
     * @return  array
     */
    public function pay(array $options, string $notify_url)
    {
        $this->options->set('notify_url', $notify_url);

        $url = '/v3/pay/transactions/app';

        $order = $this->createOrder($url, $options);

        $time = time();
        $nonce_str = Tools::createNoncestr();
        $sign = $this->getSign([$this->options['appid'], $time, $nonce_str, "prepay_id={$order['prepay_id']}"], 1);
        return [
            'appid' => $this->options['appid'],
            'partnerid' => $this->config['mch_id'],
            'prepayid' => $order['prepay_id'],
            'package' => "Sign=WXPay",
            'onoceStr' => $nonce_str,
            'timestamp' => $time,
            'paySign' => $sign
        ];
    }
}
