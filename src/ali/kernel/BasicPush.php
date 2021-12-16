<?php

namespace service\ali\kernel;

use service\exceptions\InvalidResponseException;
use service\tools\Tools;

/**
 * 移动推送基类
 * @package service\ali\kernel
 */
class BasicPush extends Basic
{
    /**
     * 服务地址
     * @var string
     */
    protected $endpoint = 'http://cloudpush.aliyuncs.com';

    /**
     * 请求参数
     * @var array
     */
    protected $params = [];

    /**
     * 可选参数参数 Key
     * @var
     */
    protected $optionalParamsKeys = [];

    /**
     * 初始化参数
     */
    public function initParam()
    {
        $this->params = [
            'Format' => 'JSON',
            'RegionId' => 'cn-hangzhou',
            'Version' => '2016-08-01',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureVersion' => '1.0'
        ];
    }

    /**
     * 设置参数
     * @param   string  $key    参数的键
     * @param   mixed   $value  参数的值
     */
    protected function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * 构建签名字符串
     * @param   array   $data
     * @return  string
     */
    protected function buildSign($data)
    {
        ksort($data);
        $signData = strtr(Tools::arr2url($data), ['+' => '%20', '*' => '%2A', '%7E' => '~']);
        $signData = "GET&%2F&" . urlencode($signData);
        return base64_encode(hash_hmac("sha1", $signData, "{$this->config['accessKey_secret']}&", true));
    }

    /**
     * 设置可选参数Keys
     * @param   array   $kays
     */
    protected function setOptionalParamsKeys($keys)
    {
        $this->optionalParamsKeys = $keys;
    }

    /**
     * 设置可选参数
     * @param   array   $data
     */
    protected function setOptionalParams($data)
    {
        foreach($data as $key => $value) {
            if (in_array($key, $this->optionalParamsKeys)) {
                $this->setParam($key, $value);
            }
        }
    }
    /**
     * 设置批量推送的可选参数
     * @param   array   $data
     */
    protected function setMassOptionalParams($data)
    {
        foreach($data as $i => $item) {
            foreach($item as $key => $value) {
                if (in_array($key, $this->optionalParamsKeys)) {
                    $index = $i + 1;
                    $this->setParam("PushTask.{$index}.{$key}", $value);
                }
            }
        }
    }

    /**
     * 发送请求
     * @return  array
     */
    protected function request()
    {
        $this->setParam('AccessKeyId', $this->config['accessKey_id']);
        $this->setParam('Timestamp', Tools::getUTCTime());
        $this->setParam('SignatureNonce', Tools::createNoncestr());
        $this->setParam('Signature', $this->buildSign($this->params));
        $result = Tools::json2arr(Tools::request('GET', $this->endpoint, [
            'query' => $this->params
        ]));
        if (!empty($result['Code'])) throw new InvalidResponseException($result['Message'], $result['Code'], $result);
        return $result;
    }
}