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
    protected static $instanceList = [];
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
        $key = md5(get_called_class() . serialize(func_get_args()));
        if (!isset(self::$instanceList[$key])) {
            self::$instanceList[$key] = new static(...func_get_args());
        }
        return self::$instanceList[$key];
    }
}