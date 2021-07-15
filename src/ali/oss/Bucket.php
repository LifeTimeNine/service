<?php

namespace service\ali\oss;

use service\ali\oss\bucket\Acl;
use service\ali\oss\bucket\Basics;
use service\ali\oss\bucket\Cors;
use service\ali\oss\bucket\Referer;
use service\ali\oss\bucket\Worm;
use service\tools\BasicBusiness;

/**
 * 关于Bucket的操作
 * @calss   Bucket
 */
class Bucket extends BasicBusiness
{
    /**
     * 基础操作
     * @param   array   $config     配置信息
     * @return  Basics
     */
    public function basics(array $config = []){
        return Basics::instance($config);
    }

    /**
     * 合规保留策略
     * @param   array   $config     配置信息
     * @return  Worm
     */
    public function worm(array $config = [])
    {
        return Worm::instance($config);
    }

    /**
     * 权限控制
     * @param   array   $config     配置信息
     * @return  Acl
     */
    public function acl(array $config = [])
    {
        return Acl::instance($config);
    }

    /**
     * 防盗链
     * @param   array   $cofnig     配置信息
     * @return  Referer
     */
    public function referer(array $config = [])
    {
        return Referer::instance($config);
    }

    /**
     * 跨域资源共享
     * @param   array   $config     配置
     * @param   Cors
     */
    public function cors(array $config = [])
    {
        return Cors::instance($config);
    }
}