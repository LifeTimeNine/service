<?php

namespace service\ali\push;

use service\ali\kernel\BasicPush;

/**
 * 标签相关接口
 * @package service\ali\push
 */
class Tag extends BasicPush
{
    /**
     * 绑定标签
     * @access public
     * @param   int             $appKey     AppKey
     * @param   string|array    $clientKey  设备或account以及alias
     * @param   string          $keyType    ClientKey的类型
     * @param   string|array    $tagName    Tag名称
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidArgumentException
     */
    public function bind(int $appKey, $clientKey, string $keyType, $tagName)
    {
        $this->setParam('Action', 'BindTag');
        $this->setParam('AppKey', $appKey);
        $this->setParam('ClientKey', is_array($clientKey) ? implode(',', $clientKey) : $clientKey);
        $this->setParam('KeyType', $keyType);
        $this->setParam('TagName', is_array($tagName) ? implode(',', $tagName) : $tagName);
        return $this->request();
    }
    /**
     * 查询标签
     * @access   public
     * @param   string          $clientKey  设备或account以及alias
     * @param   string          $keyType    ClientKey的类型
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidArgumentException
     */
    public function query(int $appKey, $clientKey, string $keyType)
    {
        $this->setParam('Action', 'QueryTags');
        $this->setParam('AppKey', $appKey);
        $this->setParam('ClientKey', $clientKey);
        $this->setParam('KeyType', $keyType);
        return $this->request();
    }
    /**
     * 解绑标签
     * @access public
     * @param   int             $appKey     AppKey
     * @param   string|array    $clientKey  设备或account以及alias
     * @param   string          $keyType    ClientKey的类型
     * @param   string|array    $tagName    Tag名称
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidArgumentException
     */
    public function unbind(int $appKey, $clientKey, string $keyType, $tagName)
    {
        $this->setParam('Action', 'UnbindTag');
        $this->setParam('AppKey', $appKey);
        $this->setParam('ClientKey', is_array($clientKey) ? implode(',', $clientKey) : $clientKey);
        $this->setParam('KeyType', $keyType);
        $this->setParam('TagName', is_array($tagName) ? implode(',', $tagName) : $tagName);
        return $this->request();
    }
    /**
     * 删除标签
     * @access public
     * @param   int             $appKey     AppKey
     * @param   string|array    $tagName    Tag名称
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidArgumentException
     */
    public function remove(int $appKey, string $tagName)
    {
        $this->setParam('Action', 'RemoveTag');
        $this->setParam('AppKey', $appKey);
        $this->setParam('TagName', $tagName);
        return $this->request();
    }
}