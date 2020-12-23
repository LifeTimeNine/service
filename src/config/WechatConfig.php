<?php
/*
 * @Description   微信相关配置
 * @Author        lifetime
 * @Date          2020-12-17 15:50:38
 * @LastEditTime  2020-12-23 17:29:28
 * @LastEditors   lifetime
 */

namespace service\config;

class WechatConfig extends BasicConfig
{
    protected $defauleConfig = [
        'official_appid' => '', // 公众号APPID
        'official_app_secret' => '', // 公众号secert

        'mch_id' => '', // 商户ID
        'mch_key' => '', // 商户支付秘钥
        'sign_type' => 'MD5', // 签名类型
    ];

    public function __construct($config)
    {
        parent::__construct();
        $this->config = array_merge($this->defauleConfig, $this->get('wechat'), $config);
    }
}
