<?php

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
     * @param   array   $config
     * @return  Service
     */
    public static function service($config = [])
    {
        return Service::instance($config);
    }

    /**
     * Bucket相关操作
     * @return Bucket
     */
    public static function bucket()
    {
        return Bucket::instance();
    }

    /**
     * Object相关操作
     * @return  Objects
     */
    public function object()
    {
        return Objects::instance();
    }
}