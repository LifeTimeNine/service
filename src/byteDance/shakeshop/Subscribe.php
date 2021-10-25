<?php

namespace service\byteDance\shakeshop;

use service\byteDance\kernel\BasicShakeShop;

/**
 * 消息订阅相关
 */
class Subscribe extends BasicShakeShop
{
    /**
     * 返回的失败Code
     * @var int
     */
    protected $returnFailCode = 1;
    /**
     * 返回的失败消息
     * @var string
     */
    protected $returnFailMsg = 'fail';
    /**
     * 验证消息
     * @param       callable    $success    成功的闭包函数
     * @param       callable    $fail       失败的闭包函数
     * @return      string
     * @reference   https://op.jinritemai.com/docs/guide-docs/153/99
     */
    public function check(callable $success, callable $fail)
    {
        // 获取并验证数据
        $data = @file_get_contents('php://input');
        if (empty($data)) {
            $this->setFailMsg('Empty data');
            $fail($this->returnFailMsg);
            return $this->getFailMsg();
        }
        // 解析数据
        $data = json_decode($data, true);
        if (json_last_error() <> 0) {
            $this->setFailMsg('Data parse fail');
            $fail($this->returnFailMsg);
            return $this->getFailMsg();
        }
        // 如果是测试消息
        if (count($data) == 1 && $data[0]['tag'] == 0 && $data[0]['msg_id'] == 0) {
            return $this->getSuccessMsg();
        }
        // 获取 Header
        $header = $this->getHeader();
        // 验证 APPKey
        if (empty($header['app-id']) || $header['app-id'] <> $this->config->get('app_key')) {
            $this->setFailMsg('app_key inconsistent');
            $fail($this->returnFailMsg);
            return $this->getFailMsg();
        }
        // 获取签名
        $eventSign = $header['event-sign']??null;
        // 按照不同的方法生成签名
        if (strtoupper($this->config->get('push_message_encrypt_method')) == 'MD5') {
            $sign = $this->config->get('app_key') . $data . $this->config->get('app_secret');
        } else {
            $sign = $this->buildPushSign($data);
        }
        // 判断签名是否一致
        if ($sign <> $eventSign) {
            $this->setFailMsg('Signature verification failure');
            $fail($this->returnFailMsg);
            return $this->getFailMsg();
        }
        // 执行成功方法
        $res = $success(json_decode($data, true));
        // 如果返回 false 则返回失败消息
        if ($res === false) {
            return $this->getFailMsg();
        } else {
            return $this->getSuccessMsg();
        }
    }

    /**
     * 获取成功消息
     * @return string
     */
    protected function getSuccessMsg()
    {
        return '{"code":0,"msg":"success"}';
    }

    /**
     * 获取失败消息
     * @return string
     */
    protected function getFailMsg()
    {
        return json_encode(['code' => $this->returnFailCode, 'msg' => $this->returnFailMsg]);
    }

    /**
     * 设置失败状态码
     * @param   int     $code
     * @return  $this
     */
    public function setFailCode(int $code)
    {
        $this->returnFailCode = $code;
        return $this;
    }
    /**
     * 设置失败消息
     * @param   string  $msg
     * @return  $this
     */
    public function setFailMsg(string $msg)
    {
        $this->returnFailMsg = $msg;
        return $this;
    }

    /**
     * 获取请求头信息
     * @return array
     */
    protected function getHeader()
    {
        if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
            $header = $result;
        } else {
            $header = [];
            $server = $_SERVER;
            foreach ($server as $key => $val) {
                if (0 === strpos($key, 'HTTP_')) {
                    $key          = str_replace('_', '-', strtolower(substr($key, 5)));
                    $header[$key] = $val;
                }
            }
            if (isset($server['CONTENT_TYPE'])) {
                $header['content-type'] = $server['CONTENT_TYPE'];
            }
            if (isset($server['CONTENT_LENGTH'])) {
                $header['content-length'] = $server['CONTENT_LENGTH'];
            }
        }
        return array_change_key_case($header);
    }

    /**
     * 计算签名
     * @param   array  $data
     * @return  string
     */
    protected function buildPushSign($data)
    {
        $arr = [
            'app_key' => $this->config->get('app_key'),
            'method' => $_GET['method']??'',
            'param_json' => json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
            'timestamp' => $data['timestamp']??null,
            'v' => $data['v']??null,
        ];
        $signStr = $this->config->get('app_secret');
        foreach($arr as $k => $v) $signStr .= $k . $v;
        $signStr .= $this->config->get('app_secret');
        return hash_hmac('sha256', $signStr, $this->config->get('app_secret'));
    }
}