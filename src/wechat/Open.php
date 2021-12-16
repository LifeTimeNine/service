<?php
/*
 * @Description   微信开放平台
 * @Author        lifetime
 * @Date          2021-09-28 08:54:25
 * @LastEditTime  2021-09-28 09:07:42
 * @LastEditors   lifetime
 */
namespace service\wechat;

use service\tools\BasicBusiness;
use service\wechat\open\Web;

/**
 * 微信开放平台
 */
class Open extends BasicBusiness
{
    /**
     * web应用
     * @return  \service\wechat\open\Web
     */
    public function web()
    {
        return Web::instance();
    }
}