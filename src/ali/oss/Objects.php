<?php

namespace service\ali\oss;

use service\ali\oss\object\Basics;
use service\ali\oss\object\Multipart;
use service\tools\BasicBusiness;

/**
 * 关于Object操作
 * @class   Objects
 */
class Objects extends BasicBusiness
{
    /**
     * 基础操作
     * @param   array   $config     配置
     * @return  Basics
     */
    public function basics(array $cofnig = [])
    {
        return Basics::instance($cofnig);
    }

    /**
     * 分片上传
     * @param   array   $config     配置
     * @return  Multipart
     */
    public function multipart(array $config = [])
    {
        return Multipart::instance($config);
    }
}