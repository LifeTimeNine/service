<?php

namespace service\byteDance\shakeshop;

use service\byteDance\kernel\BasicShakeShop;

/**
 * 订单相关API
 */
class Order extends BasicShakeShop
{
    /**
     * 订单列表查询
     * @param       int     $page       页码，0页开始
     * @param       int     $size       单页大小，限制100以内
     * @param       array   $optionals  可选参数列表
     * @return      array
     * @reference   https://op.jinritemai.com/docs/api-docs/15/1342
     */
    public function seachList(int $page, int $size, array $optionals = [])
    {
        $this->setMethodName(__FUNCTION__);
        $this->setParam([
            'page' => $page,
            'size' => $size,
        ])->setOptionals($optionals, [
            'product','b_type','after_sale_status_desc','tracking_no','presell_type',
            'order_type','create_time_start','create_time_end','abnormal_order','trade_type',
            'combine_status'=>['order_status','main_status'],'update_time_start','update_time_end',
            'order_by','order_asc',
        ]);
        
        return $this->request();
    }

    /**
     * 订单详情
     * @param   string  $orderId    店铺订单号
     * @return array
     */
    public function orderDetail(string $orderId)
    {
        $this->setMethodName(__FUNCTION__);
        $this->setParam('shop_order_id', $orderId);
        return $this->request();
    }

    /**
     * 批量解密
     * @param       array   $datas  密文列表
     * @return      array
     * @reference   https://op.jinritemai.com/docs/api-docs/15/982
     */
    public function batchDecrypt(array $datas)
    {
        $this->setMethodName(__FUNCTION__);
        $this->setParam('cipher_infos', array_reverse($datas));
        return $this->request();
    }
}