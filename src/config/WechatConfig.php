<?php
/*
 * @Description   微信相关配置
 * @Author        lifetime
 * @Date          2020-12-17 15:50:38
 * @LastEditTime  2020-12-17 18:32:48
 * @LastEditors   lifetime
 */

namespace service\config;

class WechatConfig extends BasicConfig
{
    protected $config = [
        'official_appid' => '', // 公众号APPID
        'official_app_secret' => '', // 公众号secert

        'kay' => '', // 商户支付秘钥
    ];

    public function __construct($config)
    {
        parent::__construct(array_merge($this->config, $this->getUserConfig('wechat'), $config));
    }
}
