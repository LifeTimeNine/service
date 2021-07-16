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
    private $cache;
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
        if (!empty($this->cache)) return $this->cache;
        return $this->cache = new static();
    }
}