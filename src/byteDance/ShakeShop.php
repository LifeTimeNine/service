<?php

namespace service\byteDance;

use service\byteDance\shakeshop\Order;
use service\byteDance\shakeshop\Product;
use service\byteDance\shakeshop\Push;
use service\tools\BasicBusiness;

/**
 * 抖店相关接口
 */
class ShakeShop extends BasicBusiness
{
    /**
     * 消息推送相关
     * @param   array   $config     相关配置
     * @return  \service\byteDance\shakeshop\Push
     */
    public function puhs(array $config = [])
    {
        return Push::instance($config);
    }
    /**
     * 商品相关接口
     * @param   array   $config     相关配置
     * @return \service\byteDance\shakeshop\Product
     */
    public function product(array $config = [])
    {
        return Product::instance($config);
    }

    /**
     * 订单相关接口
     * @param   array   $config     相关配置
     * @return \service\byteDance\shakeshop\Order
     */
    public function order(array $config = [])
    {
        return Order::instance($config);
    }
}