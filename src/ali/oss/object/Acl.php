<?php

namespace service\ali\oss\object;

use service\ali\kernel\BasicOss;

/**
 * 权限控制
 * @class Acl
 */
class Acl extends BasicOss
{
    /**
     * 修改文件(Object)的访问权限(ACL)
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   string  $acl                访问权限[private-私有，public-read-公共读，public-read-write-公共读写,default-继承Bucket]
     * @return  mixed
     */
    public function put(string $name='',string $endpoint='',string $fileName,string $acl)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?acl");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}?acl");
        $this->checkAcl($acl, true);
        $this->setData(self::OSS_OSS_HEADER, [
            self::OSS_OBJECT_ACL=> $acl
        ]);
        $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * 获取某个存储空间（Bucket）下的某个文件（Object）的访问权限（ACL）
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     */
    public function get(string $name='',string $endpoint='',string $fileName)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?acl");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}?acl");
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }
}