<?php
/*
 * @Description   微信H5支付
 * @Author        lifetime
 * @Date          2020-12-22 09:40:58
 * @LastEditTime  2020-12-22 09:52:24
 * @LastEditors   lifetime
 */
namespace service\wechat\pay;

use service\wechat\kernel\BasicPay;
use service\wechat\kernel\Tools;

class H5 extends BasicPay
{
    /**
     * 构造函数
     * @param   array   $config     配置参数
     */
    protected function __construct($config = [])
    {
        parent::__construct($config);
        $this->setAppId('official_appid');
    }

    /**
     * 下单支付
     * @param   array   $options    订单参数 [out_trade_no-订单编号,total_fee-订单金额，body-商品描述]
     * @param   string  $notify_url 通知地址
     * @return  array
     */
    public function pay(array $options, string $notify_url)
    {
        $this->options->set('notify_url', $notify_url);

        $this->options->set('trade_type', 'MWEB');

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