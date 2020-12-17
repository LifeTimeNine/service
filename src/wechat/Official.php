<?php
/*
 * @Description   微信公众号
 * @Author        lifetime
 * @Date          2020-12-17 16:12:58
 * @LastEditTime  2020-12-17 23:38:16
 * @LastEditors   lifetime
 */

namespace service\wechat;

use service\config\WechatConfig;
use service\exceptions\InvalidArgumentException;
use service\Tools;

class Official
{
    /**
     * 配置
     * @var DataArray
     */
    protected $config;

    /**
     * 缓存
     * @var static
     */
    protected static $cache;

    /**
     * 构造函数
     * @param   array   $config     配置参数
     */
    protected function __construct($config = [])
    {
        $this->config = new WechatConfig($config);

        if (empty($this->config['official_appid'])) throw new InvalidArgumentException("Missing Config [official_appid]");

        if (empty($this->config['official_app_secret'])) throw new InvalidArgumentException("Missing Config [official_app_secret]");
    }

    /**
     * 静态创建对象
     * @param   array   $config     配置参数
     * @return  \service\wechat\Official
     */
    public static function instance(array $config = [])
    {
        $key = md5(get_called_class() . serialize($config));
        if (isset(self::$cache[$key])) return self::$cache[$key];
        return self::$cache[$key] = new static($config);
    }

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
        // dump($url);exit;
        header($url);
        die;
    }

    /**
     *  第二步 通过code获取access_token
     * @return  array
     */
    public function getAccessToken()
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
}
