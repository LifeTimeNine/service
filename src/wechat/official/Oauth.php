<?php
/*
 * @Description   微信公众号 网页授权
 * @Author        lifetime
 * @Date          2020-12-17 16:12:58
 * @LastEditTime  2020-12-23 10:08:35
 * @LastEditors   lifetime
 */

namespace service\wechat\official;

use service\exceptions\InvalidArgumentException;
use service\tools\Cache;
use service\tools\Tools;
use service\wechat\kernel\BasicWeChat;

class Oauth extends BasicWeChat
{
    /**
     * JsApi_Ticket
     * @var string
     */
    protected $ticket = '';
    
    /**
     * 第一步
     * 请求code
     * @param   string  $redirectUri    跳转地址
     * @param   bool    $scope          是否获取用户详细信息
     * @param   string  $state          state参数
     */
    public function getCode(string $redirectUri, bool $scope = null, string $state = null)
    {
        if (empty($redirectUri)) {
            throw new InvalidArgumentException("Missing redirectUri empty");
        } else {
            $redirectUri = urlencode($redirectUri);
        }

        $scope = empty($scope) ? 'snsapi_userinfo' : 'snsapi_base';

        $url = "Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->config['official_appid']}&redirect_uri={$redirectUri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
        header($url);
        die;
    }

    /**
     *  第二步 通过code获取access_token, 注意：此access_token与基础支持的access_token不同
     * @return  array
     */
    public function getUserAccessToken()
    {
        if (empty($_GET['code'])) throw new InvalidArgumentException("Missing Option [code]");

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->config['official_appid']}&secret={$this->config['official_app_secret']}&code={$_GET['code']}&grant_type=authorization_code";

        return json_decode(Tools::request('get', $url), true);
    }

    /**
     * 刷新 access_token
     * @param   string  $refreshToken
     * @return array
     */
    public function refreshAccessToken($refreshToken)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$this->config['official_appid']}&grant_type=refresh_token&refresh_token={$refreshToken}";

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

    /**
     * 获取jsapi_ticket
     * @return   array
     */
    protected function getJsApiTicket()
    {
        $this->ticket = Cache::get("{$this->config['official_appid']}_ticket");
        if (!empty($this->ticket)) return $this->ticket;
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        $res = $this->httpGetForJson();
        Cache::set("{$this->config['official_appid']}_ticket", $res['ticket'], $res['expires_in']);
        return $this->ticket = $res['ticket'];
    }
    
    /**
     * 获取JS-SDK使用权限
     * @param   string  $url    当前网页的URL，不包含#及其后面部分
     * @return array
     */
    public function getJsSdkSign(string $url)
    {
        $data = [
            'noncestr' => Tools::createNoncestr(16),
            'jsapi_ticket' => empty($this->ticket) ? $this->getJsApiTicket() : $this->ticket,
            'timestamp' => time(),
            'utl' => $url
        ];

        ksort($data);

        $sign = sha1(Tools::arrToUrl($data));

        return [
            'appId' => $this->config['official_appid'],
            'timestamp' => $data['timestamp'],
            'nonceStr' => $data['noncestr'],
            'signature' => $sign,
        ];
    }
}
