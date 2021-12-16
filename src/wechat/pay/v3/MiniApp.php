<?php
/*
 * @Description   小程序支付
 * @Author        lifetime
 * @Date          2020-12-22 15:44:33
 * @LastEditTime  2021-10-27 16:57:45
 * @LastEditors   lifetime
 */
namespace service\wechat\pay\v3;

use service\tools\Tools;
use service\wechat\kernel\BasicPayV3;

class MiniApp extends BasicPayV3
{
    /**
     * 构造函数
     * @param   array   $config     配置参数
     */
    protected function __construct($config = [])
    {
        parent::__construct($config);
        $this->setAppId('miniapp_appid');
        $this->setMustOptions(['openid']);
    }
    /**
     * 下单支付
     * @param   array   $options    订单参数[openid-用户标识,out_trade_no-订单编号,total_fee-订单金额，body-商品描述]
     * @param   string  $notify_url 通知地址
     * @return  array
     */
    public function pay(array $options, string $notify_url)
    {
        $this->initOptions();
        $this->options->set('notify_url', $notify_url);

        $url = '/v3/pay/transactions/jsapi';


        $order = $this->createOrder($url, $options);

        $time = time();
        $nonce_str = Tools::createNoncestr();
        $sign = $this->getSign([$this->options['appid'], $time, $nonce_str, "prepay_id={$order['prepay_id']}"], 1);
        return [
            'appId' => $this->options['appid'],
            'timeStamp' => $time,
            'onoceStr' => $nonce_str,
            'package' => "prepay_id={$order['prepay_id']}",
            'signType' => 'RSA',
            'paySign' => $sign
        ];
    }
}