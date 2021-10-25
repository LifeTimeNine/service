<?php

namespace service\ali\push;

use service\ali\kernel\BasicPush;

/**
 * 别名相关接口
 * @package service\ali\push
 */
class Alias extends BasicPush
{
    /**
     * 绑定别名
     * @access public
     * @param   int             $appKey     AppKey
     * @param   string|array    $alias      别名
     * @param   string          $deviceId   设备标识
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidResponseException
     */
    public function bind(int $appKey, $alias, string $deviceId)
    {
        $this->setParam('Action', 'BindAlias');
        $this->setParam('AppKey', $appKey);
        $this->setParam('AliasName', is_array($alias) ? implode(',', $alias) : $alias);
        $this->setParam('DeviceId', $deviceId);
        return $this->request();
    }
    /**
     * 查询别名
     * @access public
     * @param   int             $appKey     AppKey
     * @param   string          $deviceId   设备标识
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidResponseException
     */
    public function query(int $appKey, string $deviceId)
    {
        $this->setParam('Action', 'QueryAliases');
        $this->setParam('AppKey', $appKey);
        $this->setParam('DeviceId', $deviceId);
        return $this->request();
    }
    /**
     * 通过别名查询设备列表
     * @access public
     * @param   int             $appKey     AppKey
     * @param   string          $alias      别名
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidResponseException
     */
    public function queryDevicesByAlias(int $appKey, $alias)
    {
        $this->setParam('Action', 'QueryDevicesByAlias');
        $this->setParam('Alias', $alias);
        $this->setParam('AppKey', $appKey);
        return $this->request();
    }
    /**
     * 解绑别名
     * @access public
     * @param   int             $appKey     AppKey
     * @param   string|array    $alias      别名
     * @param   string          $deviceId   设备标识
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidResponseException
     */
    public function unbind(int $appKey, string $deviceId, $alias)
    {
        $this->setParam('Action', 'UnbindAlias');
        $this->setParam('AppKey', $appKey);
        $this->setParam('DeviceId', $deviceId);
        $this->setParam('AliasName', is_array($alias) ? implode(',', $alias) : $alias);
        return $this->request();
    }
}