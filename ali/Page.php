<?php
/*
 * @Description   Page 支付
 * @Author        lifetime
 * @Date          2020-12-14 20:47:29
 * @LastEditTime  2020-12-14 22:31:47
 * @LastEditors   lifetime
 */
namespace service\ali;

class Page extends Basic
{

    /**
     * Page constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->options->set('method', 'alipay.trade.page.pay');
        $this->options->set('timestamp', date('Y-m-d H:i:s'));
        $this->bizContent->set('product_code', 'FAST_INSTANT_TRADE_PAY');
    }
    /**
     * 创建数据操作
     * @param   array   $order      订单信息(out_trade_no-订单编号,subject-订单名称,total_amount-订单金额)
     * @param   string  $notify_url 异步回调地址
     * @param   string  $return_url 同步跳转地址
     * @return   string
     */
    public function apply($order, $notify_url, $return_url = '')
    {
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
}