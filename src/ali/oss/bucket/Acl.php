<?php

namespace service\ali\oss\bucket;

use service\ali\kernel\BasicOss;

/**
 * 权限控制
 * @calss   Acl
 */
class Acl extends BasicOss
{
    /**
     * 设置或修改存储空间（Bucket）的访问权限（ACL）
     * @param   string  $name       Bucket名称(传空，表示从配置中获取)
     * @param   string  $acl        访问权限[private-私有，public-read-公共读，public-read-write-公共读写]
     * @return  boolean
     */
    public function put(string $name = '', string $acl)
    {
        $this->initData();
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());

        $this->checkAcl($acl);

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?acl");
        $this->setData(self::OSS_URL_PARAM, '?acl');
        $this->setData(self::OSS_OSS_HEADER, ['x-oss-acl' => $acl]);
        $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * 获取某个存储空间（Bucket）的访问权限（ACL）
     * @param   string  $name       Bucket名称(传空，表示从配置中获取)
     * @return  mixed
     */
    public function get(string $name = '')
    {
        $this->initData();
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?acl");
        $this->setData(self::OSS_URL_PARAM, '?acl');
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }
}