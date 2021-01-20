<?php
/*
 * @Description   微信公众平台相关接口
 * @Author        lifetime
 * @Date          2020-12-22 08:51:49
 * @LastEditTime  2021-01-19 13:48:04
 * @LastEditors   lifetime
 */
namespace service\wechat;

use service\tools\BasicBusiness;
use service\wechat\official\Oauth;
use service\wechat\official\Template;
use service\wechat\official\User;

/**
 * 微信公众平台
 */
class Official extends BasicBusiness
{
    /**
     * 网页授权
     * @param   array   $config     配置
     * @return  \service\wechat\official\Oauth
     */
    public function oauth(array $config = [])
    {
        return Oauth::instance($config);
    }

    /**
     * 模板消息
     * @param   array   $config     配置
     * @return \service\wechat\official\Template
     */
    public function template(array $config = [])
    {
        return Template::instance($config);
    }

    /**
     * 用户管理
     * @param   array   $config     配置
     * @return  \service\wechat\official\User
     */
    public function user(array $config = [])
    {
        return User::instance($config);
    }
}
