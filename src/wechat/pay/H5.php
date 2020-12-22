<?php
/*
 * @Description   微信H5支付
 * @Author        lifetime
 * @Date          2020-12-22 09:40:58
 * @LastEditTime  2020-12-22 17:05:53
 * @LastEditors   lifetime
 */
namespace service\wechat\pay;

use service\tools\Tools;
use service\wechat\kernel\BasicPay;

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
            'appId' => $order['appid'],
            'timeStamp' => (string)time(),
            'nonceStr' => Tools::createNoncestr(),
            'package' => "prepay_id={$order['prepay_id']}",
            'singType' => $this->options['sign_type'],
        ];
        $data['paySign'] = $this->getSign($data, $this->options['sign_type']);
        return $data;
    }
}