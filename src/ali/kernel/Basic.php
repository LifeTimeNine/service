<?php

namespace service\ali\kernel;

use service\config\AliConfig;
use service\exceptions\InvalidArgumentException;

/**
 * 阿里云基类
 * @class Basic
 */
class Basic
{
    /**
     * 配置
     * @var DataArray
     */
    protected $config;

    /**
     * 构造函数
     * @param   array   $config     配置
     */
    protected function __construct($config = [])
    {
        $this->config = new AliConfig($config);

        if (empty($this->config['accessKey_id'])) throw new InvalidArgumentException("Missing Config [accessKey_id]");
        if (empty($this->config['accessKey_secret'])) throw new InvalidArgumentException("Missing Config [accessKey_secret]");
    }

    /**
     * 静态创建对象
     * @param   array   $config     配置信息
     * @return  static
     */
    public static function instance(array $config = [])
    {
        return new static($config);
    }
}
