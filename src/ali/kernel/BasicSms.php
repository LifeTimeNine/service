<?php

namespace service\ali\kernel;

use service\exceptions\InvalidResponseException;
use service\tools\Tools;

/**
 * 短信基类
 * @class   BasicSms
 */
class BasicSms extends Basic
{
    /**
     * 服务地址
     * @var string
     */
    protected $endpoint = 'http://dysmsapi.aliyuncs.com';

    private $params = [
        'Format' => 'json',
        'SignatureVersion' => '1.0',
        'Version' => '2017-05-25',
        'SignatureMethod' => 'HMAC-SHA1'
    ];

    protected $signTemplateCode = false;

    /**
     * 获取基础参数
     * @return  array
     */
    protected function getBasicParams()
    {
        $params = $this->Params;
        $params['AccessKeyId'] = $this->config['accessKey_id'];
        $params['Timestamp'] = $this->getTime();
        $params['SignatureNonce'] = Tools::createNoncestr();
        return $params;
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
     * 数组转URL
     * @param   array   $data
     * @return  string
     */
    protected function arrToUrl($data)
    {
        $urlArr = [];
        foreach($data as $k => $v)
        {
            if (in_array($k, ['TemplateCode', 'SignName']) && !$this->signTemplateCode) continue;
            $urlArr[] = "{$k}=" . urlencode($v);
        }
        return implode('&', $urlArr);
    }

    /**
     * 构建签名字符串
     * @param   array   $data
     * @return  string
     */
    protected function buildSign($data)
    {
        ksort($data);
        $signData = strtr($this->arrToUrl($data), ['+' => '%20', '*' => '%2A', '%7E' => '~']);
        $signData = "GET&%2F&" . urlencode($signData);
        // dump($signData);
        return base64_encode(hash_hmac("sha1", $signData, "{$this->config['accessKey_secret']}&", true));
    }

    /**
     * 获取时间
     * @return  string
     */
    protected function getTime()
    {
        return gmdate("Y-m-d\TH:i:s\Z");
    }

    /**
     * 发送请求
     * @return  array
     */
    protected function request()
    {
        $this->setParam('AccessKeyId', $this->config['accessKey_id']);
        $this->setParam('Timestamp', $this->getTime());
        $this->setParam('SignatureNonce', Tools::createNoncestr());
        $this->setParam('Signature', $this->buildSign($this->params));
        $result = Tools::json2arr(Tools::request('GET', $this->endpoint, [
            'query' => $this->params
        ]));
        // if ($result['Code'] <> 'OK') throw new InvalidResponseException($result['Message'], $result['Code'], $result);
        return $result;
    }
}