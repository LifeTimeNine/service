<?php

namespace service\config;

/**
 * 七牛云相关配置
 * @class   QiniuConfig
 */
class QiniuConfig extends BasicConfig
{
    protected $defaultConfig = [];
    public function __construct($config = [])
    {
        parent::__construct();
        self::$config = array_merge($this->defaultConfig, $this->get('qiniu', []), $config);
    }
}