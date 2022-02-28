<?php
/*
 * @Description   用户手机号相关接口
 * @Author        lifetime
 * @Date          2022-02-28 18:54:11
 * @LastEditTime  2022-02-28 19:00:42
 * @LastEditors   lifetime
 */

namespace service\wechat\miniapp;

use service\exceptions\InvalidArgumentException;
use service\wechat\kernel\BasicMiniApp;

/**
 * 用户手机号相关接口
 */
class PhoneNumber extends BasicMiniApp
{
    /**
     * 获取用户手机号
     * @access  public
     * @param   string  $code   手机号获取凭证
     * @return  array
     */
    public function getUserPhoneNumber(string $code)
    {
        if (empty($code)) throw new InvalidArgumentException('Missing Options [code]');
        return $this->request('https://api.weixin.qq.com/wxa/business/getuserphonenumber', 'GET', ['data' => ['code' => $code]]);
    }
}
