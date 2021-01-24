<?php

namespace service\config;

/**
 * 七牛云相关配置
 * @class   QiniuConfig
 */
class QiniuConfig extends BasicConfig
{
    protected $defauleConfig = [];
    public function __construct($config = [])
    {
        parent::__construct();
        $this->config = array_merge($this->defauleConfig, $this->get('qiniu', []), $config);
    }
}