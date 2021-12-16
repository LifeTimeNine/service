<?php

namespace service\ali\push;

use service\ali\kernel\BasicPush;

/**
 * 账号相关接口
 * @package service\ali\push
 */
class Account extends BasicPush
{
    /**
     * 通过账户查询设备列表
     * @access public
     * @param   int             $appKey     AppKey
     * @param   string          $account    账号
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidResponseException
     */
    public function queryDevicesByAccount(int $appKey, string $account)
    {
        $this->initParam();
        $this->setParam('Action', 'QueryDevicesByAccount');
        $this->setParam('Account', $account);
        $this->setParam('AppKey', $appKey);
        return $this->request();
    }
}