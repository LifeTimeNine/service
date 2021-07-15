<?php
/*
 * @Description   微信JSApi支付
 * @Author        lifetime
 * @Date          2020-12-28 21:18:08
 * @LastEditTime  2020-12-29 09:02:48
 * @LastEditors   lifetime
 */
namespace service\wechat\pay\v3;

use service\tools\Tools;
use service\wechat\kernel\BasicPayV3;

class JsApi extends BasicPayV3
{
    /**
     * 构造函数
     * @param   array   $config     配置参数
     */
    protected function __construct($config = [])
    {
        parent::__construct($config);
        $this->setAppId('official_appid');
        $this->setMustOptions(['payer' => ['openid']]);
    }

    /**
     * 下单支付
     * @param   array   $options    订单参数[out_trade_no-订单参数, amount.total-订单金额，description-订单描述，payer.openid-用户openid]
     * @param   string  $notify_url 通知地址
     * @return  array
     */
    public function pay(array $options, string $notify_url)
    {
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
