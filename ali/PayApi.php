<?php
/*
 * @Description   阿里支付api
 * @Author        lifetime
 * @Date          2020-12-13 21:39:22
 * @LastEditTime  2020-12-13 22:29:47
 * @LastEditors   lifetime
 */

namespace service\ali;

class PayApi extends Basic
{
    public function test()
    {
        dump($this->config['sandbox']);
    }
}