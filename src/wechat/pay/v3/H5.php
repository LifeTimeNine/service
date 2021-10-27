<?php
/*
 * @Description   微信H5支付 V3.0
 * @Author        lifetime
 * @Date          2020-12-22 09:40:58
 * @LastEditTime  2021-10-27 16:57:34
 * @LastEditors   lifetime
 */
namespace service\wechat\pay\v3;

use service\tools\Tools;
use service\wechat\kernel\BasicPayV3;

class H5 extends BasicPayV3
{
    /**
     * 构造函数
     * @param   array   $config     配置参数
     */
    protected function __construct($config = [])
    {
        parent::__construct($config);
        $this->setAppId('official_appid');
        $this->setMustOptions([
            'scene_info' => [
                'payer_client_ip',
                'h5_info' => [
                    'type'
                ]
            ]
        ]);
    }

    /**
     * 下单支付
     * @param   array   $options    订单参数[out_trade_no-订单参数, amount.total-订单金额，description-订单描述，scene_info.h5_info.type-场景类型]
     * @param   string  $notify_url 通知地址
     * @return  array
     */
    public function pay(array $options, string $notify_url)
    {
        $this->initOptions();
        $this->options->set('notify_url', $notify_url);
        $this->options->merge(['scene_info' => ['payer_client_ip' => $_SERVER['REMOTE_ADDR']]], true);

        $url = '/v3/pay/transactions/h5';

        $order = $this->createOrder($url, $options);
        
        return $order['h5_url'];
    }
}