<?php

namespace service\qiniu\storage;

use service\qiniu\basic\Storage;
use service\tools\Tools;

/**
 * Bucket接口
 * @class   Bucket
 */
class Bucket extends Storage
{
    /**
     * 设置镜像源
     * @param   string  $bucketName     空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $accessUrl      镜像源的访问域名
     * @param   string  $host           回源时使用的Host头部值
     * @return  mixed
     */
    public function setSource(string $bucketName='',string $accessUrl,string $host = '')
    {
        $this->initParam();
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_UC);
        $this->setData(self::S_PATH, "/image/{$this->getBucketName($bucketName)}/from/{$this->urlBase64($accessUrl)}/host/{$this->urlBase64($host)}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 创建Bucket
     * @param   string  $bucketName      空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $region         存储区域，默认华东 华东,华北,华南,北美,东南亚
     * @return  mixed
     */
    public function create(string $bucketName,string $region = '华东')
    {
        $this->initParam();
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_UC);
        $this->checkRegion($region);
        $this->setData(self::S_PATH, "/mkbucketv3/{$bucketName}/region/" . self::S_REGION_LIST[$region][0]);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 删除Bucket
     * @param   string  $bucketName      空间名称(传空表示从配置中获取[storage_bucketName])
     * @return mixed
     */
    public function delete(string $bucketName)
    {
        $this->initParam();
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_UC);
        $this->setData(self::S_PATH, "/drop/{$bucketName}");
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 获取 Bucket 空间域名
     * @param   string  $bucketName      空间名称(传空表示从配置中获取[storage_bucketName])
     * @return  array
     */
    public function getDomain(string $bucketName = '')
    {
        $this->initParam();
        $this->setData(self::S_METHOD, self::S_GET);
        $this->setData(self::S_HOST, self::S_HOST_API);
        $this->setData(self::S_PATH, "/v6/domain/list?tbl={$this->getBucketName($bucketName)}");
        return $this->request($this->bulidMgSign());
    }

    /**
     * 设置 Bucket 访问权限
     * @param   string  $bucketName      空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   boolean $private        是否是私有
     * @return  boolean
     */
    public function setAccessType(string $bucketName = '',bool $private)
    {
        $this->initParam();
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_UC);
        $this->setData(self::S_PATH, "/private");
        $this->setData(self::S_BODY, Tools::arrToUrl([
            'bucket' => $this->getBucketName($bucketName),
            'private' => $private ? 1: 0,
        ]));
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 设置空间标签
     * @param   string  $bucketName      空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   array   $tags           标签 [{key}=>{value},{key}=>{value}]
     * @return  boolean
     */
    public function setTags(string $bucketName = '', array $tags)
    {
        $this->initParam();
        $this->setData(self::S_METHOD, self::S_PUT);
        $this->setData(self::S_HOST, self::S_HOST_UC);
        $this->setData(self::S_PATH, "/bucketTagging");
        $this->setData(self::S_QUERY, [
            'bucket' => $this->getBucketName($bucketName)
        ]);
        $tagData = [];
        foreach($tags as $k => $v)
        {
            $tagData[] = ['Key' => $k, "Value" => $v];
        }
        $this->setData(self::S_BODY, Tools::arr2json([
            'Tags' => $tagData
        ]));
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_JOSN);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 查询指定空间已设置的标签信息
     * @param   string  $bucketName      空间名称(传空表示从配置中获取[storage_bucketName])
     * @return  array
     */
    public function getTgas(string $bucketName = '')
    {
        $this->initParam();
        $this->setData(self::S_METHOD, self::S_GET);
        $this->setData(self::S_HOST, self::S_HOST_UC);
        $this->setData(self::S_PATH, "/bucketTagging?bucket={$this->getBucketName($bucketName)}");
        return $this->request($this->bulidMgSign());
    }

    /**
     * 删除指定空间的所有标签
     * @param   string  $bucketName      空间名称(传空表示从配置中获取[storage_bucketName])
     * @return  boolean
     */
    public function deleteTags(string $bucketName = '')
    {
        $this->initParam();
        $this->setData(self::S_METHOD, self::S_DELETE);
        $this->setData(self::S_HOST, self::S_HOST_UC);
        $this->setData(self::S_PATH, "/bucketTagging?bucket={$this->getBucketName($bucketName)}");
        $this->request($this->bulidMgSign());
        return true;
    }
}