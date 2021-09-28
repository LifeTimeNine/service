<?php
/*
 * @Description   微信开放平台web应用
 * @Author        lifetime
 * @Date          2021-09-28 08:56:57
 * @LastEditTime  2021-09-28 09:01:10
 * @LastEditors   lifetime
 */
namespace service\wechat\open;

use service\tools\BasicBusiness;
use service\wechat\open\web\Login;

/**
 * web应用
 */
class Web extends BasicBusiness
{
    /**
     * 登录
     * @param   array   $config     配置
     * @return  \service\wechat\open\web\Login
     */
    public function login(array $config = [])
    {
        return Login::instance($config);
    }
}
