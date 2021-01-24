<?php

namespace service\ali\kernel;

use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;
use service\tools\Tools;

/**
 * OSS基类
 * @class BasicOss
 */
class BasicOss extends Basic
{
    /**
     * 内部常量
     */
    const OSS_METHOD = 'method';
    const OSS_BODY = 'body';
    const OSS_DATE = 'Date';
    const OSS_CONTENT_TYPE = 'Content-Type';
    const OSS_CONTENT_MD5 = 'Content-Md5';
    const OSS_CONTENT_LENGTH = 'Content-Length';
    const OSS_AUTHORIZATION = 'Authorization';
    const OSS_HOST = 'Host';
    const OSS_ACL = 'x-oss-acl';
    const OSS_OBJECT_ACL = 'x-oss-object-acl';
    const OSS_OSS_HEADER = 'oss_header';
    const OSS_RESOURCE = 'resource';
    const OSS_ENDPOINT = 'endpoint';
    const OSS_BUCKET_NAME = 'bucketName';
    const OSS_RESPONSE_CODE = 'response_code';
    const OSS_URL_PARAM = 'url_param';
    const OSS_CACHE_CONTROL = 'Cache-Control';
    const OSS_DISPOSITION = 'Content-Disposition';
    const OSS_ENCODEING = 'Content-Encoding';
    const OSS_ETAG = 'ETag';
    const OSS_EXPIRES = 'Expires';
    const OSS_FORBID_OVERWIRTE = 'x-oss-forbid-overwrite';
    const OSS_RESPONSE_CONTENT_TYPE = 'response-content-type';
    const OSS_RESPONSE_LANGUAGE = 'response-content-language';
    const OSS_RESPONSE_EXPIRES = 'response-expires';
    const OSS_RESPONSE_CACHE = 'response-cache-control';
    const OSS_RESPONSE_DISPONSITON = 'response-content-disposition';
    const OSS_RESPONSE_ENCODE = 'response-content-encoding';
    const OSS_RANGE = 'Range';
    const OSS_STROAGE_CLASS = 'x-oss-storage-class';
    /**
     * http method
     */
    const OSS_HTTP_GET = 'GET';
    const OSS_HTTP_PUT = 'PUT';
    const OSS_HTTP_POST = 'POST';
    const OSS_HTTP_HEAD = 'HEAD';
    const OSS_HTTP_DELETE = 'DELETE';
    const OSS_HTTP_OPTIONS = 'OPTIONS';

    /**
     * content-type
     */
    const OSS_CONTENT_TYPE_HTML = 'text/html';
    const OSS_CONTENT_TYPE_XML = 'application/xml';
    const OSS_CONTENT_TYPE_PLAIN = 'text/plain';
    const OSS_CONTENT_TYPE_URLENCODEED = 'application/x-www-form-urlencoded';
    const OSS_CONTENT_TYPE_FORMDATA = 'multipart/form-data';

    /**
     * emdpoint list
     */
    const OSS_ENDPOINT_LIST = [
        'oss-cn-hangzhou.aliyuncs.com'      => '华东1（杭州）',
        'oss-cn-shanghai.aliyuncs.com'      => '华东2（上海）',
        'oss-cn-qingdao.aliyuncs.com'       => '华北1（青岛）',
        'oss-cn-beijing.aliyuncs.com'       => '华北2（北京）',
        'oss-cn-zhangjiakou.aliyuncs.com'   => '华北 3（张家口）',
        'oss-cn-huhehaote.aliyuncs.com'     => '华北5（呼和浩特）',
        'oss-cn-wulanchabu.aliyuncs.com'    => '华北6（乌兰察布）',
        'oss-cn-shenzhen.aliyuncs.com'      => '华南1（深圳）',
        'oss-cn-heyuan.aliyuncs.com'        => '华南2（河源）',
        'oss-cn-guangzhou.aliyuncs.com'     => '华南3（广州）',
        'oss-cn-chengdu.aliyuncs.com'       => '西南1（成都）',
        'oss-cn-hongkong.aliyuncs.com'      => '中国（香港）',
        'oss-us-west-1.aliyuncs.com'        => '美国西部1（硅谷）',
        'oss-us-east-1.aliyuncs.com'        => '美国东部1（弗吉尼亚）',
        'oss-ap-southeast-1.aliyuncs.com'   => '亚太东南1（新加坡）',
        'oss-ap-southeast-2.aliyuncs.com'   => '亚太东南2（悉尼）',
        'oss-ap-southeast-3.aliyuncs.com'   => '亚太东南3（吉隆坡）',
        'oss-ap-southeast-5.aliyuncs.com'   => '亚太东南5（雅加达）',
        'oss-ap-northeast-1.aliyuncs.com'   => '亚太东北1（日本）',
        'oss-ap-south-1.aliyuncs.com'       => '亚太南部1（孟买）',
        'oss-eu-central-1.aliyuncs.com'     => '欧洲中部1（法兰克福）',
        'oss-eu-west-1.aliyuncs.com'        => '英国（伦敦）',
        'oss-me-east-1.aliyuncs.com'        => '中东东部1（迪拜）'
    ];
    /**
     * allow acl
     */
    const OSS_ALLOW_ACL = ['private', 'public-read', 'public-read-write'];
    /**
     * allow storage calss
     */
    const OSS_ALLOW_STORAGE_CLASS = ['Standard', 'IA', 'Archive', 'ColdArchive'];

    /**
     * 构造函数
     */
    protected function __construct($config = [])
    {
        parent::__construct($config);
        $this->setData(self::OSS_DATE, $this->getDate());
    }

    /**
     * 数据
     * @var array
     */
    private $data = [];

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
     * 设置数据
     * @param   string  $name   名
     * @param   mixed   $value  值
     */
    protected function setData($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * 获取数据
     * @param   string  $name   名
     * @param   boolean $error  不存在时是否抛出异常
     * @return  mixed
     */
    protected function getData($name, $error = false)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } elseif ($error === false) {
            return null;
        }
        throw new InvalidArgumentException("The {$name} parameter is not set");
    }

    /**
     * 验证ACL权限
     * @param   string     $acl
     * @param   boolean    $hasDefault
     * @return  true
     */
    protected function checkAcl($acl, $hasDefault = false) {
        if(!in_array($acl, self::OSS_ALLOW_ACL) && $hasDefault && $acl <> 'default') {
            throw new InvalidArgumentException("Unknown acl {$acl}");
        }
        return true;
    }

    /**
     * 验证储存类型
     * @param   string  $storageClass
     */
    protected function checkStorageClass($storageClass)
    {
        if(!in_array($storageClass, self::OSS_ALLOW_STORAGE_CLASS)) {
            throw new InvalidArgumentException("Unknown StorageClass {$storageClass}");
        }
        return true;
    }

    /**
     * 获取加密过的消息内容
     * @return  string
     */
    protected function getContentMd5()
    {
        $body = $this->getData(self::OSS_BODY);
        return $body === null ? "" : base64_encode(md5($body, true));
    }

    /**
     * 获取签名字符串
     * @param   array|string   $data   签名数据
     * @return   string
     */
    protected function getSign($data)
    {
        $signData = is_array($data) ? implode('', $data) : $data;
        return base64_encode(hash_hmac("sha1", $signData, $this->config['accessKey_secret'], true));
    }

    /**
     * 构建签名数据
     * @return  string
     */
    protected function buildSign()
    {
        $signData = [];

        $method = $this->getData(self::OSS_METHOD, true);
        $signData[] = "{$method}\n";

        $signData[] =  "{$this->getContentMd5()}\n";

        $contentType = $this->getData(self::OSS_CONTENT_TYPE);
        $signData[] = "{$contentType}\n";

        $signData[] = "{$this->getData(self::OSS_DATE)}\n";

        $ossHeader = $this->getData(self::OSS_OSS_HEADER);
        if ($ossHeader !== null) $signData[] = $this->getOssHeaderStr();

        $resource = $this->getData(self::OSS_RESOURCE, true);
        $signData[] = $resource;

        dump($signData);

        return "OSS {$this->config['accessKey_id']}:{$this->getSign($signData)}";
    }

    /**
     * 构建header
     */
    protected function buildHeader()
    {
        $headers = [self::OSS_AUTHORIZATION => $this->buildSign()];
        $headers[self::OSS_CONTENT_MD5] = $this->getContentMd5();
        $headers[self::OSS_CONTENT_LENGTH] = strlen($this->getData(self::OSS_BODY));
        if ($this->getData(self::OSS_CONTENT_TYPE) !== null) $headers[self::OSS_CONTENT_TYPE] = $this->getData(self::OSS_CONTENT_TYPE);
        $headers[self::OSS_DATE] = $this->getData(self::OSS_DATE);
        if ($this->getData(self::OSS_OSS_HEADER)!== null) {
            foreach ($this->getData(self::OSS_OSS_HEADER) as $k => $v) {
                $headers[$k] = $v;
            }
        }
        $headers[self::OSS_HOST] = $this->getData(self::OSS_HOST);
        if ($this->getData(self::OSS_DISPOSITION) !== null) $headers[self::OSS_DISPOSITION] = $this->getData(self::OSS_DISPOSITION);
        if ($this->getData(self::OSS_CACHE_CONTROL) !== null) $headers[self::OSS_CACHE_CONTROL] = $this->getData(self::OSS_CACHE_CONTROL);
        if ($this->getData(self::OSS_EXPIRES) !== null) $headers[self::OSS_EXPIRES] = $this->getData(self::OSS_EXPIRES);
        if ($this->getData(self::OSS_ETAG) !== null) $headers[self::OSS_ETAG] = $this->getData(self::OSS_ETAG);
        if ($this->getData(self::OSS_ENCODEING) !== null) $headers[self::OSS_ENCODEING] = $this->getData(self::OSS_ENCODEING);
        if ($this->getData(self::OSS_RESPONSE_CONTENT_TYPE) !== null) $headers[self::OSS_RESPONSE_CONTENT_TYPE] = $this->getData(self::OSS_RESPONSE_CONTENT_TYPE);
        if ($this->getData(self::OSS_RESPONSE_LANGUAGE) !== null) $headers[self::OSS_RESPONSE_LANGUAGE] = $this->getData(self::OSS_RESPONSE_LANGUAGE);
        if ($this->getData(self::OSS_RESPONSE_EXPIRES) !== null) $headers[self::OSS_RESPONSE_EXPIRES] = $this->getData(self::OSS_RESPONSE_EXPIRES);
        if ($this->getData(self::OSS_RESPONSE_CACHE) !== null) $headers[self::OSS_RESPONSE_CACHE] = $this->getData(self::OSS_RESPONSE_CACHE);
        if ($this->getData(self::OSS_RESPONSE_ENCODE) !== null) $headers[self::OSS_RESPONSE_ENCODE] = $this->getData(self::OSS_RESPONSE_ENCODE);
        if ($this->getData(self::OSS_RESPONSE_DISPONSITON) !== null) $headers[self::OSS_RESPONSE_DISPONSITON] = $this->getData(self::OSS_RESPONSE_DISPONSITON);
        if ($this->getData(self::OSS_RANGE) !== null) $headers[self::OSS_RANGE] = $this->getData(self::OSS_RANGE);
        $headerData = [];
        foreach($headers as $k => $v) {
            $headerData[] = "{$k}: {$v}";
        }
        return $headerData;
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
     * 获取地域节点
     * @param   string  $endpoint   输入的地域节点
     * @return  string
     */
    protected function getEndponit($endpoint = null)
    {
        if (empty($endpoint)) $endpoint = $this->config['oss_endpoint'];
        if (empty($endpoint)) throw new InvalidArgumentException("Missing Options [endpoint]");
        if (!array_key_exists($endpoint, self::OSS_ENDPOINT_LIST)) throw new InvalidArgumentException("Unknown endpoint {$endpoint}");
        return $endpoint;
    }

    /**
     * 获取Bucket名称
     * @param   string  $name   输入的名称
     * @return  string
     */
    protected function getName($name)
    {
        if (empty($name)) $name = $this->config['oss_bucketName'];
        if (empty($name)) throw new InvalidArgumentException("Missing Options [name]");
        return $name;
    }

    /**
     * 获取参数
     * @param   array   $data       输入的数据
     * @param   array   $keys       需要获取的key
     * @param   mixed   $default    默认值(为false时表示这一项为假，就不再返回这一项)
     * @return  array
     */
    protected function getParams($data, $keys, $default = false)
    {
        $params = [];
        foreach($keys as $key) {
            if (!empty($data[$key])) {
                $params[$key] = $data[$key];
            }
            if (empty($data[$key]) && $default !== false) {
                $params[$key] = $default;
            }
        }
        return $params;
    }

    /**
     * 获取ossHeader字符串
     * @param   array   $data       输入的值
     * @return  string
     */
    protected function getOssHeaderStr() {
        $data = $this->getData(self::OSS_OSS_HEADER);
        $str = '';
        foreach($data as $key => $value) {
            $str .= strtolower(trim($key)).':'.trim($value)."\n";
        }
        return $str;
    }

    /**
     * 发送请求
     * @param   array   $query
     * @param   boolean $format
     * @return  mixed
     */
    protected function request($query = [], $format = true)
    {
        $method = $this->getData(self::OSS_METHOD, true);
        $endpoint = $this->getData(self::OSS_ENDPOINT, true);
        $bucketName = $this->getData(self::OSS_BUCKET_NAME);
        if ($bucketName !== null) {
            $host = "{$bucketName}.{$endpoint}";
        } else {
            $host = $endpoint;
        }
        $this->setData(self::OSS_HOST, $host);
        $headers = $this->buildHeader();
        $url = "{$this->getProtocol()}{$host}{$this->getData(self::OSS_URL_PARAM)}";
        $options = [
            'query' => !empty($query) ? $query : [],
            'headers' => $headers,
            'data' => $this->getData(self::OSS_BODY),
        ];
        $result = $this->sendRequest($method, $url, $options);
        if (strpos($result, 'Code') !== false) {
            $result = Tools::xml2arr($result);
            throw new InvalidResponseException($result['Message'], $result['Code'], $result);
        } 
        return $format ? Tools::xml2arr($result) : $result;
    }

    /**
     * CURL模拟网络请求
     * @param string $method 请求方法
     * @param string $url 请求方法
     * @param array $options 请求参数[headers,query,data,ssl_cer,ssl_key]
     * @return string
     * @throws Exception
     */
    protected function sendRequest(string $method, string $url, array $options = [])
    {
        $curl = curl_init();
        // GET参数设置
        if (!empty($options['query'])) {
            $url .= (stripos($url, '?') !== false ? '&' : '?') . http_build_query($options['query']);
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
        if (!empty($options['data'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $options['data']);
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
        $this->setData(self::OSS_RESPONSE_CODE, curl_getinfo($curl, CURLINFO_HTTP_CODE));
        curl_close($curl);
        return $content;
    }
}