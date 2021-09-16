<?php

namespace service\tools;

/**
 * 业务基类
 * @calss   BasicBusiness
 */
class BasicBusiness
{
    /**
     * 缓存
     * @var array
     */
    private static $cache = [];
    /**
     * 构造函数
     */
    private function __construct()
    {
        
    }
    /**
     * 静态创建对象
     * @return  $this
     */
    public static function instance()
    {
        $key = md5(get_called_class());
        if (isset(static::$cache[$key])) return static::$cache[$key];
        return static::$cache[$key] = new static;
    }
}