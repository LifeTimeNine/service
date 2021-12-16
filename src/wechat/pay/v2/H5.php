<?php
/*
 * @Description   微信H5支付
 * @Author        lifetime
 * @Date          2020-12-22 09:40:58
 * @LastEditTime  2021-10-27 16:33:40
 * @LastEditors   lifetime
 */
namespace service\wechat\pay\v2;

use service\exceptions\InvalidResponseException;
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
        $this->setMustOptions(['scene_info']);
    }

    /**
     * 下单支付
     * @param   array   $options    订单参数 [out_trade_no-订单编号,total_fee-订单金额，body-商品描述, scene_info-场景信息]
     * @param   string  $notify_url 通知地址
     * @return  array
     */
    public function pay(array $options, string $notify_url)
    {
        $this->initOptions();
        $this->options->set('notify_url', $notify_url);

        $this->options->set('trade_type', 'MWEB');

        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

        $order = $this->createOrder($url, $options);

        if (!empty($order['return_code']) && !empty($order['result_code'])  && $order['return_code'] == "SUCCESS" && $order['result_code'] == "SUCCESS") {
            return $order['mweb_url'];
        } else {
            throw new InvalidResponseException("Response Fail", 0, $order);
        }
    }
}