<?php

namespace service\ali;

use service\ali\push\Account;
use service\ali\push\Alias;
use service\ali\push\Push as PushPush;
use service\ali\push\Tag;
use service\tools\BasicBusiness;

/**
 * 移动推送服务
 * @package service\ali
 */
class Push extends BasicBusiness
{
    /**
     * 推送相关接口
     * @param   array   $config     配置信息
     * @return \service\ali\push\Push
     */
    public function push($config = [])
    {
        return PushPush::instance($config);
    }
    /**
     * 标签相关接口
     * @param   array   $config     配置信息
     * @return \service\ali\push\Tag
     */
    public function tag($config = [])
    {
        return Tag::instance($config);
    }
    /**
     * 别名相关接口
     * @param  array   $config     配置信息
     * @return \service\ali\push\Alias
     */
    public function alias($config = [])
    {
        return Alias::instance($config);
    }
    /**
     * 账号相关接口
     * @param   array   $config     配置信息
     * @return \service\ali\push\Account
     */
    public function account($config = [])
    {
        return Account::instance($config);
    }
}