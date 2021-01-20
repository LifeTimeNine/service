<?php

namespace service\ali\oss\bucket;

use service\ali\kernel\BasicOss;
use service\exceptions\InvalidArgumentException;
use service\tools\Tools;

/**
 * 基础操作
 * @calss   Basics
 */
class Basics extends BasicOss
{
    /**
     * 容灾类型
     * #var array
     */
    protected static $disaster = ['LRS', 'ZRS'];

    /**
     * 创建Bucket
     * @param   string  $name           名称
     * @param   string  $endpoint       地域节点 (传空，表示从配置中获取)
     * @param   string  $acl            访问权限[private-私有，public-read-公共读，public-read-write-公共读写]
     * @param   string  $storageClass   储存类型[Standard（标准存储，默认值）IA（低频访问）Archive（归档存储）ColdArchive（冷归档存储）]
     * @param   string  $disaster       容灾类型[LRS(本地容灾,默认值) ZRS(同城容灾)]
     * @return  boolean
     */
    public function putBucket(string $name, string $endpoint = '', string $acl = 'private', string $storageClass = 'Standard', string $disaster = 'LRS')
    {
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        if (empty($name)) throw new InvalidArgumentException("Missing Options [name]");
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_RESOURCE, "/{$name}/");
        $this->checkAcl($acl);
        $this->checkStorageClass($storageClass);
        $this->checkDisaster($disaster);
        $this->setData(self::OSS_BODY, Tools::arr2xml([
            'StorageClass' => $storageClass,
            'DataRedundancyType' => $disaster
        ]));
        $this->setData(self::OSS_OSS_HEADER, [
            'x-oss-acl' => $acl,
        ]);
        $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * 删除Bucket
     * @param   string  $name       名称
     * @param   string  $endpoint   区域节点
     * @return  boolean
     */
    public function deleteBucket(string $name, string $endpoint = '')
    {
        if (empty($name)) throw new InvalidArgumentException("Missing Options [name]");
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_DELETE);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/");
        $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '204' ? true : false;
    }

    /**
     * 获取Bucket中所有的Object
     * @param   string  $name           名称 (传空，表示从配置中获取)
     * @param   string  $endpoint       区域节点 (传空，表示从配置中获取)
     * @param   string  $delimiter      对Object名字进行分组的字符
     * @param   string  $marker         设定从marker之后按字母排序开始返回Object
     * @param   string  $max_keys       指定返回Object的最大数
     * @param   string  $prefix         前缀
     * @param   string  $encoding_type  对返回的内容进行编码并指定编码的类型
     * @return  mixed
     */
    public function getBucket(string $name = '', string $endpoint='', string $delimiter='', string $marker='', int $max_keys = 100, string $prefix = '', string $encoding_type = 'url')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/");
        $res = $this->request([
            'delimiter' => $delimiter,
            'marker' => $marker,
            'max-keys' => $max_keys,
            'prefix' => $prefix,
            'encoding-type' => $encoding_type
        ]);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }
    
    /**
     * 获取Bucket中获取所有的Object(V2)
     * @param   string  $name               名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $delimiter          对Object名字进行分组的字符
     * @param   string  $start_after        设定从Start-after之后按字母排序开始返回Object
     * @param   string  $continuation_token 指定list操作需要从此token开始
     * @param   int     $mak_keys           指定返回Object的最大数
     * @param   string  $prefix             前缀
     * @param   string  $encoding_type      对返回的内容进行编码并指定编码的类型
     * @param   string  $fetch_owner        指定是否在返回结果中包含owner信息
     * @return  mixed
     */
    public function getBucketV2(string $name='', string $endpoint = '', string $delimiter = '', string $start_after='', string $continuation_token='', int $max_keys=100, string $prefix='', string $encoding_type = 'url', bool $fetch_owner = false)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?continuation-token");
        $res = $this->request([
            'list-type' => '2',
            'delimiter' => $delimiter,
            'start-after' => $start_after,
            'continuation-token' => $continuation_token,
            'max-keys' => $max_keys,
            'prefix' => $prefix,
            'encoding-type' => $encoding_type,
            'fetch-owner' => $fetch_owner ? 'true' : 'false',
        ]);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }

    /**
     * 获取Bucket相关信息
     * @param   string  $name       名称(传空，表示从配置中获取)
     * @param   string  $endpoint   区域节点(传空，表示从配置中获取)
     * @return  mixed
     */
    public function getBucketInfo(string $name = '', string $endpoint = '')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?bucketInfo");
        $this->setData(self::OSS_URL_PARAM, '?bucketInfo');
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }

    /**
     * 获取Bucket位置信息
     * @param   string  $name       名称(传空，表示从配置中获取)
     * @param   string  $endpoint   区域节点(传空，表示从配置中获取)
     * @return  mixed
     */
    public function getBucketLocation(string $name = '', string $endpoint = '')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?location");
        $this->setData(self::OSS_URL_PARAM, '?location');
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res[0] : false;
    }

    /**
     * 验证容灾类型
     * @param   string  $disaster
     */
    protected function checkDisaster($disaster)
    {
        if (!in_array($disaster, self::$disaster)) {
            throw new InvalidArgumentException("Unknown DataRedundancyType {$disaster}");
        }
    }
}