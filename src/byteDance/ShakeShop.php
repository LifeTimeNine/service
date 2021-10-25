<?php

namespace service\byteDance;

use service\byteDance\shakeshop\Order;
use service\byteDance\shakeshop\Product;
use service\byteDance\shakeshop\Subscribe;
use service\tools\BasicBusiness;

/**
 * 抖店相关接口
 */
class ShakeShop extends BasicBusiness
{
    /**
     * 消息订阅相关
     * @param   array   $config     相关配置
     * @return  \service\byteDance\shakeshop\Subscribe
     */
    public function subscribe(array $config = [])
    {
        return Subscribe::instance($config);
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