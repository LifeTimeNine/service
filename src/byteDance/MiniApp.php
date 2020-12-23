<?php
/*
 * @Description   字节小程序相关接口
 * @Author        lifetime
 * @Date          2020-12-23 10:29:46
 * @LastEditTime  2020-12-23 10:33:44
 * @LastEditors   lifetime
 */
namespace service\byteDance;

use service\byteDance\kernel\BasicMiniApp;
use service\tools\Tools;

/**
 * 字节小程序相关接口
 */
class MiniApp extends BasicMiniApp
{
    /**
     * @description 获取 session_key 和 openId （code 和 anonymous_code 至少要有一个）
     * @param   string  $code login 接口返回的登录凭证
     * @param   string  $anonymous_code login 接口返回的匿名登录凭证
     * @return array [session_key, openid]
     */
    public function code2Session($code = '', $anonymous_code = '')
    {
        if (empty($code) && empty($anonymous_code)) throw new \Exception("Code or anonymous_code should have at least one");
        $requestData = Tools::request('get', 'https://developer.toutiao.com/api/apps/jscode2session', [
            'query' => [
                'appid' => $this->config['appid'],
                'secret' => $this->config['secret'],
                'code' => $code,
                'anonymous_code' => $anonymous_code
            ],
        ]);
        return Tools::json2arr($requestData);
    }

    /**
     * 验证用户信息
     * @param  string   $sign           抖音加密密文
     * @param  string   $rawData        serInfo 的 JSON 字符串形式
     * @param  string   $session_key
     * @return bool
     */
    public function checkUserInfo($sign, $rawData, $session_key)
    {
        return sha1("{$rawData}{$session_key}") == $sign;
    }
}