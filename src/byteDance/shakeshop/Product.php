<?php

namespace service\byteDance\shakeshop;

use service\byteDance\kernel\BasicShakeShop;

/**
 * 商品相关API
 * @package \service\byteDance\shakeshop
 */
class Product extends BasicShakeShop
{
    /**
     * 获取商品列表新版
     * @param   int     $page       第几页（第一页为1，最大为100）
     * @param   int     $size       每页返回条数，最多支持100条
     * @param   array   $optionals  可选参数列表
     * @return  array
     * @desc    参考 https://op.jinritemai.com/docs/api-docs/14/633
     */
    public function listV2(int $page,int $size, array $optionals = [])
    {
        $this->setMethodName(__FUNCTION__);
        $this->setParam([
            'page' => $page,
            'size' => $size,
        ])->setOptionals($optionals, [
            'page','size','status','check_status','product_type',
            'start_time','end_time','update_start_time','update_end_time',
        ]);

        return $this->request();
    }
}