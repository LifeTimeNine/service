<?php
/*
 * @Description   阿里支付
 * @Author        lifetime
 * @Date          2020-12-13 21:39:22
 * @LastEditTime  2020-12-16 00:16:22
 * @LastEditors   lifetime
 */

namespace service\ali;

class Pay extends Basic
{
    /**
     * Web 页面支付
     * @param   array   $order      订单信息(out_trade_no-订单编号,subject-订单名称,total_amount-订单金额)
     * @param   string  $notify_url 异步回调地址
     * @param   string  $return_url 同步跳转地址
     * @return   string
     */
    public function page($order, $notify_url, $return_url = '')
    {
        $this->options->set('method', 'alipay.trade.page.pay');
        $this->bizContent->set('product_code', 'FAST_INSTANT_TRADE_PAY');

        $this->options->set('notify_url', $notify_url);
        $this->options->set('return_url', $return_url);
        $this->options->set('biz_content', json_encode($this->bizContent->merge($order, true), 256));
        $this->options->set('sign', $this->getSign());

        $html = "<form id='alipaysubmit' name='alipaysubmit' action='{$this->gateway}' method='post'>";
        foreach ($this->options->get() as $key => $value) {
            $value = str_replace("'", '&apos;', $value);
            $html .= "<input type='hidden' name='{$key}' value='{$value}'/>";
        }
        $html .= "<input type='submit' value='ok' style='display:none;'></form>";
        return "{$html}<script>document.forms['alipaysubmit'].submit();</script>";
    }

    /**
     * APP 支付
     * @param   array   $order      订单信息(out_trade_no-订单编号,subject-订单名称,total_amount-订单金额)
     * @param   string  $notify_url 异步回调地址(为空可能会支付失败)
     * @return   string
     */
    public function app($order, $notify_url)
    {
        $this->options->set('method', 'alipay.trade.app.pay');
        $this->options->set('alipay_sdk', 'alipay-sdk-php-20200415');
        $this->bizContent->set('product_code', 'QUICK_MSECURITY_PAY');

        $this->options->set('notify_url', $notify_url);
        $this->options->set('biz_content', json_encode($this->bizContent->merge($order, true), 256));
        $this->options->set('sign', $this->getSign());

        return http_build_query($this->options->get());
    }
}