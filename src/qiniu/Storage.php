<?php

namespace service\qiniu;

use service\qiniu\storage\Bucket;
use service\qiniu\storage\Objects;
use service\qiniu\storage\Service;
use service\tools\BasicBusiness;

/**
 * 对象存储相关服务
 * @class Storage
 */
class Storage extends BasicBusiness
{
    /**
     * Service相关接口
     * @param   array   $config     配置
     * @return Service
     */
    public function service(array $config = [])
    {
        return Service::instance($config);
    }

    /**
     * Bucket 相关接口
     * @param   array   $cofnig     配置
     * @return  Bucket
     */
    public function bucket(array $config = [])
    {
        return Bucket::instance($config);
    }

    /**
     * Object 相关接口
     * @param   array   $cofnig     配置
     * @return  Objects
     */
    public function object(array $config = [])
    {
        return Objects::instance($config);
    }
}