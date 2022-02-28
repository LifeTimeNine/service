<?php
/*
 * @Description   微信小程序相关接口
 * @Author        lifetime
 * @Date          2021-01-16 19:46:31
 * @LastEditTime  2022-02-28 19:02:51
 * @LastEditors   lifetime
 */
namespace service\wechat;

use service\tools\BasicBusiness;
use service\wechat\miniapp\Login;
use service\wechat\miniapp\PhoneNumber;
use service\wechat\miniapp\UserInfo;

/**
 * 微信小程序相关接口
 * @class   MiniApp
 */
class MiniApp extends BasicBusiness
{
    /**
     * 登录功能
     * @param   array   $config     配置
     * @return  Login
     */
    public function login(array $config = [])
    {
        return Login::instance($config);
    }

    /**
     * 用户信息功能
     * @param   array   $config     配置
     * @return  UserInfo
     */
    public function userInfo(array $config = [])
    {
        return UserInfo::instance($config);
    }

    /**
     * 手机号功能
     * @param   array   $config     配置
     * @return  PhoneNumber
     */
    public function phoneNumber(array $config = [])
    {
        return PhoneNumber::instance($config);
    }
}