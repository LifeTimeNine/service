<?php
/*
 * @Description   字节小程序相关接口
 * @Author        lifetime
 * @Date          2020-12-23 10:29:46
 * @LastEditTime  2020-12-23 16:09:58
 * @LastEditors   lifetime
 */

namespace service\byteDance;

use service\byteDance\kernel\BasicMiniApp;
use service\exceptions\InvalidRequestException;
use service\tools\Tools;

/**
 * 字节小程序
 */
class MiniApp extends BasicMiniApp
{
    /**
     * 获取 session_key 和 openId （code 和 anonymous_code 至少要有一个）
     * @param   string  $code login 接口返回的登录凭证
     * @param   string  $anonymous_code login 接口返回的匿名登录凭证
     * @return array [session_key, openid]
     */
    public function code2Session(string $code = null, string $anonymous_code = null)
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
    public function checkUserInfo(string $sign, array $rawData, string $session_key)
    {
        return sha1("{$rawData}{$session_key}") == $sign;
    }

    /**
     * 获取小程序或小游戏的二维码
     * @param   string  $savePath   保存路径
     * @param   string  $appname    是打开二维码的字节系 app 名称，默认toutiao
     * @param   string  $path       小程序/小游戏启动参数，小程序则格式为 encode({path}?{query})，小游戏则格式为 JSON 字符串，默认为空
     * @param   int     $wdith       宽度,默认430
     * @param   array   $lineColor  二维码线条颜色
     * @param   array   $backround  二维码背景颜色
     * @param   bool    $setIcon    是否展示小程序/小游戏 icon，默认不展示
     * @return 
     */
    public function createQRCode(string $savePath, string $appname = 'toutiao', string $path = null, int $width = 430, array $lineColor = [], array $backround = [], bool $setIcon = false)
    {
        $url = 'https://developer.toutiao.com/api/apps/qrcode';
        $result = Tools::request('post', $url, [
            'data' => [
                'access_token' => $this->getAccessToken(),
                'appname' => $appname,
                'path' => $path,
                'width' => $width,
                'line_color' => $lineColor,
                'background' => $backround,
                'set_icon' => $setIcon
            ]
        ]);

        if (strpos($result, 'errcode') === false) {
            $dir = dirname($savePath);
            if (is_dir($dir)) {
                @mkdir($dir, 0777, true);
                file_put_contents($savePath, $result);
            }
        } else {
            $result = Tools::json2arr($result);
            if ($result['errcode'] <> 0) throw new InvalidRequestException($result['errmsg'], $result['errcode'], $result);
        }
    }
}
