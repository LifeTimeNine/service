<?php
/*
 * @Description   微信 APP支付
 * @Author        lifetime
 * @Date          2020-12-22 15:23:40
 * @LastEditTime  2020-12-28 20:34:00
 * @LastEditors   lifetime
 */
namespace service\wechat\pay\v2;

use service\tools\Tools;
use service\wechat\kernel\BasicPay;

class App extends BasicPay
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
     * @param   array   $options    订单参数 [out_trade_no-订单编号,total_fee-订单金额，body-商品描述]
     * @param   array   $notify_url 通知地址
     * @return  array
     */
    public function pay(array $options, string $notify_url)
    {
        $this->options->set('notify_url', $notify_url);

        $this->options->set('trade_type', 'APP');

        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

        $order = $this->createOrder($url, $options);

        $data = [
            'appid' => $order['appid'],
            'partnerid' => $order['mch_id'],
            'prepayid' => (string)$order['prepay_id'],
            'package'   => 'Sign=WXPay',
            'timestamp' => (string)time(),
            'noncestr' => Tools::createNoncestr(),
        ];
        $data['sign'] = $this->getSign($data, $this->config['sign_type']);

        return $data;
    }
}
