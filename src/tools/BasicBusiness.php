<?php

namespace service\tools;

/**
 * 业务基类
 * @calss   BasicBusiness
 */
class BasicBusiness
{
    /**
     * 静态创建对象
     * @return  $this
     */
    public static function instance()
    {
        return new static();
    }
}