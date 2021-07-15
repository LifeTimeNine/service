<?php
/*
 * @Description   登录相关接口
 * @Author        lifetime
 * @Date          2021-01-16 20:02:52
 * @LastEditTime  2021-01-16 20:33:11
 * @LastEditors   lifetime
 */
namespace service\wechat\miniapp;

use service\exceptions\InvalidResponseException;
use service\tools\Tools;
use service\wechat\kernel\BasicMiniApp;

/**
 * 小程序登录相关接口
 * @calss   Login
 */
class Login extends BasicMiniApp
{
    /**
     * 登录凭证校验
     * @param   string  $code   登录时获取的 code
     * @return  array
     */
    public function code2Session(string $code)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $restlt = Tools::json2arr(Tools::request('GET', $url, [
            'query' => [
                'appid' => $this->config['miniapp_appid'],
                'secret' => $this->config['miniapp_app_secret'],
                'js_code' => $code,
                'grant_type' => 'authorization_code'
            ]
        ]));
        if (!empty($restlt['openid']) && !empty($restlt['session_key'])) {
            return $restlt;
        }
        throw new InvalidResponseException($restlt['errmsg'], $restlt['errcode'], $restlt);
    }
}
