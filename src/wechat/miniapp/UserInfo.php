<?php
/*
 * @Description   用户信息相关接口
 * @Author        lifetime
 * @Date          2021-01-16 20:02:52
 * @LastEditTime  2021-01-16 22:00:30
 * @LastEditors   lifetime
 */

namespace service\wechat\miniapp;

use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;
use service\tools\Tools;
use service\wechat\kernel\BasicMiniApp;

/**
 * 用户信息相关接口
 * @calss   Login
 */
class UserInfo extends BasicMiniApp
{
    /**
     * 验证用户信息
     * @param   string      $rawData        数据字符串
     * @param   string      $signature      签名字符串
     * @param   string      $session_key    session_key
     * @return  boolean
     */
    public function check($rawData, $signature, $session_key)
    {
        return sha1("{$rawData}{$session_key}") == $signature;
    }
    /**
     * 用户信息解密
     * @param   string      $encryptedData  密文
     * @param   string      $iv             解密算法初始向量
     * @param   string      $session_key    session_key
     * @return  array
     */
    public function decodeUserInfo($encryptedData, $iv, $session_key)
    {
        if (strlen($session_key) <> 24) throw new InvalidArgumentException('Missing Options [session_key]');
        if (strlen($iv) > 24) throw new InvalidArgumentException('Missing Options [iv]');
        $aesCipher = base64_decode($encryptedData);
        $aeskey = base64_decode($session_key);
        $aesIv = base64_decode($iv);
        return Tools::json2arr(openssl_decrypt($aesCipher, 'AES-128-CBC', $aeskey, 1, $aesIv));
    }
    /**
     * 用户支付完成后，获取该用户的 UnionId
     * @param   array   $options    参数[openid-用户唯一标识, transaction_id-微信支付订单号, out_trade_no-微信支付商户订单号](单号二选一)
     * @return  array
     */
    public function getPaidUnionId(array $options)
    {
        if (empty($this->config['mch_id'])) throw new InvalidArgumentException('Missing Config [mcn_id]');
        if (empty($options['openid'])) throw new InvalidArgumentException("Missing Options [openid]");
        $requestOptions = [
            'openid' => $options['openid'],
            'mch_id' => $this->config['mch_id'],
        ];
        if (!empty($options['transaction_id'])) {
            $requestOptions['transaction_id'] = $options['transaction_id'];
        } elseif (!empty($options['out_trade_no'])) {
            $requestOptions['out_trade_no'] = $options['out_trade_no'];
        } else {
            throw new InvalidArgumentException('Missing options [transaction_id OR out_trade_no]');
        }
        return $this->request('https://api.weixin.qq.com/wxa/getpaidunionid', 'GET', ['query' => $requestOptions]);
    }
}
