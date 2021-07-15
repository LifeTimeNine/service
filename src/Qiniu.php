<?php

namespace service;

use service\qiniu\Storage;

/**
 * 七牛云相关服务
 * @class   Qiniu
 */
class Qiniu
{
    /**
     * 对象存储
    * @param    array   $config     配置参数
     * @return  Storage
     */
    public static function storage(array $config = [])
    {
        return Storage::instance($config);
    }
}