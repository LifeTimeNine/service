<?php

namespace service\qiniu\basic;

use service\config\QiniuConfig;
use service\exceptions\InvalidArgumentException;

/**
 * 七牛云基类
 * @class   Qiniu
 */
class Basic
{
    /**
     * 配置
     * @var DataArray
     */
    protected $config;

    /**
     * 缓存
     * @var static
     */
    protected static $cache;

    /**
     * 构造函数
     * @param   array   $config     配置
     */
    protected function __construct($config = [])
    {
        $this->config = new QiniuConfig($config);

        if (empty($this->config['accessKey'])) throw new InvalidArgumentException("Missing Config [accessKey]");
        if (empty($this->config['secretKey'])) throw new InvalidArgumentException("Missing Config [secretKey]");
    }

    /**
     * 静态创建对象
     * @param   array   $config     配置信息
     * @return  static
     */
    public static function instance(array $config = [])
    {
        $key = md5(get_called_class() . serialize($config));
        if (isset(self::$cache[$key])) return self::$cache[$key];
        return self::$cache[$key] = new static($config);
    }

    /**
     * URL安全的Base64编码
     * @param   string  $str        要签名的字符串
     * @return  string
     */
    protected function urlBase64($str)
    {
        if (empty($str)) return $str;
        return strtr(base64_encode($str), ['+' => '-', '/' => '_']);
    }

    /**
     * 内部常量
     */
    const S_METHOD = 'method';
    const S_PATH = 'path';
    const S_HOST = 'Host';
    const S_QUERY = 'query';
    const S_CONTENT_TYPE = 'Content-Type';
    const S_CONTENT_LENGTH = "Content-Length";
    const S_QINIU_HEADER = 'qiniu_header';
    const S_BODY = 'body';
    const S_RESPONSE_CODE = 'response_code';
    const S_DATE = 'Date';
    const S_AUTHORIZATION = 'Authorization';

    /**
     * HOST
     */
    const S_HOST_API = 'api.qiniu.com';
    const S_HOST_RS = 'rs.qiniu.com';
    const S_HOST_UC = 'uc.qbox.me';
    const S_HOST_RSF = 'rsf.qiniu.com';
    /**
     * 请求方法
     */
    const S_GET = 'GET';
    const S_POST = 'POST';
    const S_PUT = 'PUT';
    const S_DELETE = 'DELETE';
    /**
     * content-type
     */
    const S_CONTENT_TYPE_STREAM = 'application/octet-stream';
    const S_CONTENT_TYPE_URLENCODE = 'application/x-www-form-urlencoded';
    const S_CONTENT_TYPE_JOSN = 'application/json';
    const S_CONTENT_TYPE_FORMDATA = 'multipart/form-data';
    const S_CONTENT_TYPE_OCTETSTREAM = 'application/octet-stream';
}