<?php

namespace service\ali\oss\object;

use service\ali\kernel\BasicOss;
use service\tools\Tools;

/**
 * 分片上传
 * @class   Multipart
 */
class Multipart extends BasicOss
{
    /**
     * 初始化一个Multipart Upload事件
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   string  $cache              该Object被下载时的网页的缓存行为
     * @param   string  $disposition        该Object被下载时的名称
     * @param   string  $encodeing          该Object被下载时的内容编码格式
     * @param   int     $expires            过期时间，单位为毫秒
     * @param   boolean $overwrite          是否覆盖同名Object
     * @param   string  $storageClass       Object的存储类型
     * @return  mixed
     */
    public function init(string $name='',string $endpoint='',string $fileName,string $cache='',string $disposition='',string $encodeing='',int $expires=null,bool $overwrite=null,string $storageClass='')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_POST);
        $this->setData(self::OSS_CONTENT_TYPE, Tools::getMimetype($fileName));
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?uploads");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}?uploads");
        if (!empty($cache)) $this->setData(self::OSS_CACHE_CONTROL, $cache);
        if (!empty($disposition)) $this->setData(self::OSS_DISPOSITION, $disposition);
        if (!empty($encodeing)) $this->setData(self::OSS_ENCODEING, $encodeing);
        if (!empty($expires)) $this->setData(self::OSS_EXPIRES, $expires);
        $ossHeader = [];
        if (!empty($overwrite)) $ossHeader[self::OSS_FORBID_OVERWIRTE] = $overwrite ? 'true' : 'false';
        if (!empty($storageClass)) $ossHeader[self::OSS_ALLOW_STORAGE_CLASS] = $storageClass;
        if (!empty($ossHeader)) $this->setData(self::OSS_OSS_HEADER, $ossHeader);
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }

    /**
     * 分块（Part）上传数据
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   int     $partNumber         Part标识
     * @param   string  $uploadId           上传唯一标识
     * @param   string  $data               数据
     */
    public function part($name='',$endpoint='',$fileName,$partNumber,$uploadId,$data)
    {

    }
}