<?php
/*
 * @Description   字节小程序相关接口
 * @Author        lifetime
 * @Date          2020-12-23 10:29:46
 * @LastEditTime  2021-01-17 00:03:39
 * @LastEditors   lifetime
 */

namespace service\byteDance;

use service\Ali;
use service\byteDance\kernel\BasicMiniApp;
use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;
use service\tools\Tools;
use service\WeChat;

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
        if (empty($code) && empty($anonymous_code)) throw new InvalidArgumentException("Code or anonymous_code should have at least one");
        $requestData = Tools::request('get', 'https://developer.toutiao.com/api/apps/jscode2session', [
            'query' => [
                'appid' => $this->config['miniapp_appid'],
                'secret' => $this->config['miniapp_secret'],
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
     * 获取支付订单信息
     * @param   array   $options    订单参数 ['out_order_no', 'uid', 'total_amount', 'subject', 'body', 'trade_time', 'valid_time', 'notify_url']
     * @param   int     $options    支付类型
     * @return  array
     */
    public function getPayOrderInfo(array $options, int $service = 1)
    {
        $mustOptions = ['out_order_no', 'uid', 'total_amount', 'subject', 'body', 'trade_time', 'valid_time', 'notify_url'];
        $options = array_merge([
            'merchant_id' => $this->config['miniapp_pay_mch_id'],
            'app_id' => $this->config['miniapp_pay_appid'],
            'sign_type' => 'MD5',
            'timestamp' => (string)time(),
            'version' => '2.0',
            'trade_type' => 'H5',
            'product_code' => 'pay',
            'payment_type' => 'direct',
            'currency' => 'CNY',
            'alipay_url' => '',
            'wx_url' => '',
            'wx_type' => 'MWEB',
            'risk_info' => json_encode(['ip' => $_SERVER['REMOTE_ADDR']])
        ], $options);
        Tools::checkOptions($options, $mustOptions);
        switch($service) {
            case 1:
            break;
            case 3:
                $options['wx_url'] = WeChat::pay()->h5()->pay([
                    'out_trade_no' => $options['out_order_no'],
                    'total_fee' => $options['total_amount'],
                    'body' => $options['subject'],
                    'time_start' => date('YmdHis', $options['trade_time']),
                    'time_expire' => date('YmdHis', $options['trade_time'] + $options['valid_time']),
                    'scene_info' => json_encode(['h5_info' => ['type' => Tools::getClientType()]]),
                ], $options['notify_url']);
            break;
            case 4:
                $options['alipay_url'] = Ali::pay()->app([
                    'out_trade_no' => $options['out_order_no'],
                    'subject' => $options['subject'],
                    'total_amount' => $options['total_amount'],
                    'body' => $options['body'],
                    'time_expire' => date('Y-m-d H:i', $options['trade_time'] + $options['valid_time']),
                ], $options['notify_url']);
            break;
        }
        $options['sign'] = $this->getPaySign($options, $this->config['miniapp_pay_secret']);

        return $options;
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
    public function createQRCode(string $savePath, string $appname = 'toutiao', string $path = '', int $width = 430, array $lineColor = [], array $backround = [], bool $setIcon = false)
    {
        $url = 'https://developer.toutiao.com/api/apps/qrcode';
        $data = [
            'access_token' => $this->getAccessToken(),
            'appname' => $appname,
            'with' => $width,
            'set_icon' => $setIcon
        ];
        if (!empty($path)) $data['path'] = $path;
        if (!empty($lineColor)) $data['line_color'] = $lineColor;
        if (!empty($backround)) $data['background'] = $backround;
        $result = Tools::request('post', $url, [
            'headers' => [
                'Content-Type: application/json'
            ],
            'data' => Tools::arr2json($data)
        ]);

        if (strpos($result, 'errcode') === false) {
            $dir = dirname($savePath);
            if (is_dir($dir)) {
                @mkdir($dir, 0777, true);
                file_put_contents($savePath, $result);
            }
        } else {
            $result = Tools::json2arr($result);
            if ($result['errcode'] <> 0) throw new InvalidResponseException($result['errmsg'], $result['errcode'], $result);
        }
    }
}
