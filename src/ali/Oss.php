<?php
/*
 * @Description   
 * @Author        lifetime
 * @Date          2021-07-15 10:49:45
 * @LastEditTime  2021-07-15 10:49:53
 * @LastEditors   lifetime
 */

namespace service\ali;

use service\ali\oss\Bucket;
use service\ali\oss\Objects;
use service\ali\oss\Service;
use service\tools\BasicBusiness;

/**
 * 阿里云 OSS相关操作
 * @class OSS
 */
class Oss extends BasicBusiness
{
    /**
     * Service相关操作
     * @param   array   $config 配置参数
     * @return  Service
     */
    public static function service($config = [])
    {
        return Service::instance($config);
    }

    /**
     * Bucket相关操作
     * @param   array   $config 配置参数
     * @return Bucket
     */
    public static function bucket($config = [])
    {
        return Bucket::instance($config);
    }

    /**
     * Object相关操作
     * @param   array   $config 配置参数
     * @return  Objects
     */
    public function object($config = [])
    {
        return Objects::instance($config);
    }
}