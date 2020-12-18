<?php
/*
 * @Description   阿里支付
 * @Author        lifetime
 * @Date          2020-12-13 21:39:22
 * @LastEditTime  2020-12-18 22:45:34
 * @LastEditors   lifetime
 */

namespace service\ali;

use service\ali\kernel\Basic;
use service\exceptions\InvalidArgumentException;

class Pay extends Basic
{
    /**
     * Web 页面支付
     * @param   array   $order      订单信息(out_trade_no-订单编号,subject-订单名称,total_amount-订单金额)
     * @param   string  $notify_url 异步回调地址
     * @param   string  $return_url 同步跳转地址
     * @return   string
     */
    public function page(array $order, string $notify_url, string $return_url = null)
    {
        $this->checkOrder($order);

        $this->options->set('method', 'alipay.trade.page.pay');
        $this->bizContent->set('product_code', 'FAST_INSTANT_TRADE_PAY');

        $this->options->set('notify_url', $notify_url);
        $this->options->set('return_url', $return_url);
        
        return $this->buildPayHtml($order);
    }

    /**
     * 手机网站支付
     * @param   array   $order      订单信息(out_trade_no-订单编号,subject-订单名称,total_amount-订单金额)
     * @param   string  $notify_url 异步回调地址
     * @param   string  $return_url 同步跳转地址
     * @return  string
     */
    public function wap(array $order, string $notify_url, string $return_url = null)
    {
        $this->checkOrder($order);

        $this->options->set('method', 'alipay.trade.wap.pay');
        $this->bizContent->set('product_code', 'FAST_INSTANT_TRADE_PAY');

        $this->options->set('notify_url', $notify_url);
        $this->options->set('return_url', $return_url);

        return $this->buildPayHtml($order);
    }

    /**
     * APP 支付
     * @param   array   $order      订单信息(out_trade_no-订单编号,subject-订单名称,total_amount-订单金额)
     * @param   string  $notify_url 异步回调地址(为空可能会支付失败)
     * @return   string
     */
    public function app(array $order, string $notify_url)
    {
        $this->checkOrder($order);
        
        $this->options->set('method', 'alipay.trade.app.pay');
        $this->options->set('alipay_sdk', 'alipay-sdk-php-20200415');
        $this->bizContent->set('product_code', 'QUICK_MSECURITY_PAY');

        $this->options->set('notify_url', $notify_url);

        $this->options->set('biz_content', json_encode($this->bizContent->merge($order, true), 256));
        $this->options->set('sign', $this->getSign());

        return http_build_query($this->options->get());
    }

    /**
     * 异步通知处理
     * @param callable  $callback   验证完成闭包函数(参数: $data-数据，$checkRes-验证结果) 如果返回false强制给阿里云返回失败的消息
     * @param string    给阿里返回的消息
     */
    public function notify(callable $callback= null)
    {
        $data = $_POST;
        $checkRes = $this->verify($data);

        if ($callback !== null) {
            $callbackRes = $callback($data, $checkRes);
            return $callbackRes === false ? 'fail' : 'success';
        }
        return $checkRes ? 'success' : 'fail';
    }

    /**
     * 订单查询
     * @param array     $options    请求参数 (out_trade_no-商户订单号, trade_no-支付宝订单号)
     * @param callable  $callback   查询完成闭包函数(参数: $data-支付宝返回的数据, $checkRes-签名验证结果)
     */
    public function query(array $options, callable $callback)
    {
        if (empty($options['out_trade_no']) && empty($options['trade_no'])) {
            throw new InvalidArgumentException("Missing Options [out_trade_no OR trade_no]");
        }

        $this->options->set('method', 'alipay.trade.query');

        list($data, $checkRes) = $this->requestAli($options);

        $callback($data, $checkRes);
    }

    /**
     * 退款
     * @param   array       $options    请求参数 (out_trade_no-商户订单号, trade_no-支付宝订单号, refund_amount-退款金额, out_request_no-退款请求号)
     * #param   callable    $callback   退款完成闭包函数*(参数: $data-支付宝返回的数据, $checkRes-签名验证结果)
     */
    public function refund(array $options, callable $callback)
    {
        if (empty($options['out_trade_no']) && empty($options['trade_no'])) {
            throw new InvalidArgumentException("Missing Options [out_trade_no OR trade_no]");
        }

        if (empty($options['refund_amount']) || $options['refund_amount'] <= 0) {
            throw new InvalidArgumentException("Missing Options [refund_amount]");
        }

        if (empty($options['out_request_no'])) {
            throw new InvalidArgumentException("Missing options [out_request_no]");
        }

        $this->options->set('method', 'alipay.trade.refund');

        list($data, $checkRes) = $this->requestAli($options);

        $callback($data, $checkRes);
    }

    /**
     * 退款查询
     * @param array     $options    请求参数 (out_trade_no-商户订单号, trade_no-支付宝订单号, out_request_no-退款请求号)
     * @param callable  $callback   查询完成闭包函数(参数: $data-支付宝返回的数据, $checkRes-签名验证结果)
     */
    public function refundQuery(array $options, callable $callback)
    {
        if (empty($options['out_trade_no']) && empty($options['trade_no'])) {
            throw new InvalidArgumentException("Missing Options [out_trade_no OR trade_no]");
        }

        if (empty($options['out_request_no'])) {
            throw new InvalidArgumentException("Missing options [out_request_no]");
        }

        $this->options->set('method', 'alipay.trade.fastpay.refund.query');

        list($data, $checkRes) = $this->requestAli($options);

        $callback($data, $checkRes);
    }
}