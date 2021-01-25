<?php

namespace service\qiniu\basic;

use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;
use service\tools\Tools;

use function Qiniu\base64_urlSafeEncode;

/**
 * 七牛云对象存储基类
 * @class   Storage
 */
class Storage extends Basic
{
    /**
     * 请求数据
     * @var array
     */
    private $data = [];

    /**
     * 设置请求数据
     * @param   string  $key    键
     * @param   mixed   $value  值
     */
    protected function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * 获取请求数据
     * @param   string  $key    键
     * @param   boolean $error  未设置时是否抛出错误
     * @return  mixed
     */
    protected function getData($key, $error = false)
    {
        if (isset($this->data[$key])) return $this->data[$key];
        if ($error) throw new InvalidArgumentException("Missing Options [{$key}]");
        return null;
    }

    /**
     * 获取请求协议
     * @return  string
     */
    protected function getProtocol()
    {
        return empty($this->config['isSsl']) ? 'http://' : 'https://';
    }

    /**
     * 获取指定时间戳的时间
     * @param   int     $timestamp      时间戳
     * @return  string
     */
    protected function getDate($timestamp = null)
    {
        return (empty($timestamp) ? gmdate("D, d M Y H:i:s") : gmdate("D, d M Y H:i:s", $timestamp)) . " GMT";
    }

    /**
     * 获取Bucket名称
     * @param   string  $name   名称
     * @return  string
     */
    protected function getBucketName($name)
    {
        if (empty($name)) $name = $this->config['storage_bucketName'];
        if (empty($name)) throw new InvalidArgumentException("Missing Options [bucketName]");
        return $name;
    }

    /**
     * 获取访问域名
     * @return string
     */
    protected function getDomain()
    {
        $domain = $this->config['storage_domain'];
        if (empty($domain)) throw new InvalidArgumentException("Missing Config [storage_domain]");
        return $domain;
    }
    /**
     * 验证区域
     * @param   string  @region
     * @return  boolean
     */
    protected function checkRegion($region)
    {
        if (!array_key_exists($region, self::S_REGION_LIST)) throw new InvalidArgumentException("Unknown region {$region}");
        return true;
    }

    /**
     * 构建管理凭证
     * @return  string
     */
    protected function bulidMgSign()
    {
        $signStr = "{$this->getData(self::S_METHOD, true)} {$this->getData(self::S_PATH, true)}";
        if ($this->getData(self::S_QUERY) !== null) $signStr .= ("?". Tools::arrToUrl($this->getData(self::S_QUERY)));
        $signStr .= "\nHost: {$this->getData(self::S_HOST, true)}";
        if ($this->getData(self::S_CONTENT_TYPE) !== null) $signStr .= "\nContent-Type: {$this->getData(self::S_CONTENT_TYPE)}";
        if ($this->getData(self::S_QINIU_HEADER) !== null) {
            foreach($this->getData(self::S_QINIU_HEADER) as $k => $v)
            {
                $signStr .= "\n{$k}: {$v}";
            }
        }
        $signStr .= "\n\n";
        if ($this->getData(self::S_BODY) !== null && $this->getData(self::S_CONTENT_TYPE) <> self::S_CONTENT_TYPE_STREAM) {
            $signStr .= $this->getData(self::S_BODY);
        }
        $sign = $this->urlBase64(hash_hmac('sha1', $signStr, $this->config['secretKey'], true));
        return "Qiniu {$this->config['accessKey']}:{$sign}";
    }

    /**
     * 构建上传凭证
     * @return string
     */
    protected function buildUploadSign()
    {
        $signStr = $this->urlBase64(Tools::arr2json($this->getData(self::S_UPLOAD_STARTEGY, true)));
        $sign = $this->urlBase64(hash_hmac('sha1', $signStr, $this->config['secretKey'], true));
        return "{$this->config['accessKey']}:{$sign}:{$signStr}";
    }

    /**
     * 构建header
     * @param   string  $authorization  认证信息
     * @return  array
     */
    protected function bulidHeader($authorization)
    {
        $header = [
            self::S_DATE => $this->getDate(),
            self::S_HOST => $this->getData(self::S_HOST, true),
            self::S_AUTHORIZATION => $authorization
        ];
        if ($this->getData(self::S_CONTENT_TYPE) !== null) $header[self::S_CONTENT_TYPE] = $this->getData(self::S_CONTENT_TYPE);
        if ($this->getData(self::S_BODY) !== null) $header[self::S_CONTENT_LENGTH] = strlen($this->getData(self::S_BODY));

        $headerData = [];
        foreach($header as $k => $v) $headerData[] = "{$k}: {$v}";

        return $headerData;
    }

    /**
     * 发起请求
     * @param   string  $authorization  认证信息
     * @param   boolean $format         格式化结果
     * @return  mixed
     */
    protected function request($authorization, $format = true)
    {
        $result = $this->sendRequest($this->getData(self::S_METHOD), "{$this->getProtocol()}{$this->getData(self::S_HOST)}{$this->getData(self::S_PATH)}", [
            'headers' => $this->bulidHeader($authorization),
            'query' => $this->getData(self::S_QUERY),
            'body' => $this->getData(self::S_BODY),
        ]);
        if ($this->getData(self::S_RESPONSE_CODE) <> 200) {
            $this->getData(self::S_RESPONSE_CODE)<>404&&$result = Tools::json2arr($result);
            throw new InvalidResponseException($this->getData(self::S_RESPONSE_CODE)==404?$result:$result['error'],empty($result['code'])?0:$result['code'], $result);
        }
        return $format ? Tools::json2arr($result) : $result;
    }

    /**
     * CURL模拟网络请求
     * @param string $method 请求方法
     * @param string $url 请求方法
     * @param array $options 请求参数[headers,query,body,ssl_cer,ssl_key]
     * @return string
     * @throws Exception
     */
    private function sendRequest(string $method, string $url, array $options = [])
    {
        $curl = curl_init();
        // GET参数设置
        if (!empty($options['query'])) {
            $url .= (stripos($url, '?') !== false ? '&' : '?') . Tools::arrToUrl($options['query']);
        }
        // CURL头信息设置
        if (!empty($options['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        }
        switch (strtoupper($method)) {
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                break;
            case 'HEAD':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
                curl_setopt($curl, CURLOPT_NOBODY, true);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
                break;
        }
        if (!empty($options['body'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $options['body']);
        }
        // 证书文件设置
        if (!empty($options['ssl_cert'])) {
            if (file_exists($options['ssl_cert'])) {
                curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
                curl_setopt($curl, CURLOPT_SSLCERT, $options['ssl_cert']);
            } else {
                throw new \Exception("Certificate files that do not exist. --- [ssl_cert]");
            }
        }
        // 证书文件设置
        if (!empty($options['ssl_key'])) {
            if (file_exists($options['ssl_key'])) {
                curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
                curl_setopt($curl, CURLOPT_SSLKEY, $options['ssl_key']);
            } else {
                throw new \Exception("Certificate files that do not exist. --- [ssl_key]");
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HEADER, strtoupper($method) == 'HEAD');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $content = curl_exec($curl);
        $this->setData(self::S_RESPONSE_CODE, curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $content;
    }

    const S_UPLOAD_STARTEGY = 'upload_startegy';

    /**
     * 区域列表
     */
    const S_REGION_LIST = [
        '华东' => ['z0', 'up.qiniup.com', 'upload.qiniup.com'],
        '华北' => ['z1', 'up-z1.qiniup.com', 'upload-z1.qiniup.com'],
        '华南' => ['z2', 'up-z2.qiniup.com', 'upload-z2.qiniup.com'],
        '北美' => ['na0', 'up-na0.qiniup.com', 'upload-na0.qiniup.com'],
        '东南亚' => ['as0', 'up-as0.qiniup.com', 'upload-as0.qiniup.com']
    ];
}