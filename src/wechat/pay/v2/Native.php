<?php
/*
 * @Description   Native支付
 * @Author        lifetime
 * @Date          2020-12-21 16:27:38
 * @LastEditTime  2021-10-27 16:33:57
 * @LastEditors   lifetime
 */
namespace service\wechat\pay\v2;

use Endroid\QrCode\QrCode;
use service\wechat\kernel\BasicPay;

class Native extends BasicPay
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
     * @param   int     $qrcodeWith 二维码宽度,默认200
     * @return  array
     */
    public function pay(array $options, string $notify_url, int $qrcodeWith = 200)
    {
        $this->initOptions();
        $this->options->set('notify_url', $notify_url);

        $this->options->set('trade_type', 'NATIVE');

        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';


        $order = $this->createOrder($url, $options);

        $qrcode = new QrCode();
        $qrcode->setText($order['code_url'])
            ->setExtension('png')
            ->setSize($qrcodeWith);
        $order['qrcode'] = 'data:png;base64,' . base64_encode($qrcode->get('png'));
        
        return $order;
    }
}