<?php
/*
 * @Description   网站应用登录
 * @Author        lifetime
 * @Date          2021-09-27 17:37:19
 * @LastEditTime  2021-09-28 09:42:09
 * @LastEditors   lifetime
 */
namespace service\wechat\open\web;

use service\exceptions\InvalidArgumentException;
use service\tools\Tools;
use service\wechat\kernel\BasicOpen;

/**
 * 网站应用登录服务
 */
class Login extends BasicOpen
{
    /**
     * 获取Code （第一步）
     * @param   string  $redirectUri    跳转地址
     * @param   string  $state          state参数
     */
    public function getCode(string $redirectUri, string $state = 'STATE')
    {
        if (empty($redirectUri)) {
            throw new InvalidArgumentException("Missing redirectUri empty");
        } else {
            $redirectUri = urlencode($redirectUri);
        }
        $url = "Location: https://open.weixin.qq.com/connect/qrconnect?appid={$this->getConfig()['appid']}&redirect_uri={$redirectUri}&response_type=code&scope=snsapi_login&state={$state}#wechat_redirect";
        header($url);
        Tools::endResponse();
    }

    /**
     * 通过code获取access_token（第二步）
     * @return array
     */
    public function getAccessToken()
    {
        if (empty($_GET['code'])) throw new InvalidArgumentException("Missing Option [code]");
        $config = $this->getConfig();
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$config['appid']}&secret={$config['app_secret']}&code={$_GET['code']}&grant_type=authorization_code";

        return json_decode(Tools::request('get', $url), true);
    }

    /**
     * 刷新 access_token
     * @param   string  $refreshToken
     * @return array
     */
    public function refreshAccessToken($refreshToken)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$this->getConfig()['appid']}&grant_type=refresh_token&refresh_token={$refreshToken}";

        return json_decode(Tools::request('get', $url), true);
    }

    /**
     * 获取用户个人信息（UnionID机制）
     * @param string $accessToken   调用凭证
     * @param string $openid        用户标识
     * @return array
     */
    public function getUserinfo($accessToken, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$accessToken}&openid={$openid}";
        return json_decode(Tools::request('get', $url), true);
    }

    /**
     * 校验授权凭证是否有效
     * @param   string  $accessToken    调用凭证
     * @param   string  $openid         用户标识
     * @return  array
     */
    public function checkAccessToken(string $accessToken, string $openid)
    {
        $url = "https://api.weixin.qq.com/sns/auth?access_token={$accessToken}&openid={$openid}";
        return json_decode(Tools::request('get', $url), true);
    }
}
