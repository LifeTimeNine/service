<?php
/*
 * @Description   抖音 基类
 * @Author        lifetime
 * @Date          2020-12-23 09:19:28
 * @LastEditTime  2020-12-23 09:32:44
 * @LastEditors   lifetime
 */
namespace service\byteDance\kernel;

use service\config\ByteDanceConfig;

class BasicTouTiao
{
    /**
     * 配置
     * @var DataArray
     */
    protected $config;

    /**
     * 缓存
     * @var static
     */
    protected static $cache;

    /**
     * 构造函数
     * @param   array   $cofing     配置信息
     */
    protected function __construct($config = [])
    {
        $this->config = new ByteDanceConfig($config);
    }
}