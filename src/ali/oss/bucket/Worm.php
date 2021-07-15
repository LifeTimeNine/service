<?php

namespace service\ali\oss\bucket;

use service\ali\kernel\BasicOss;
use service\tools\Tools;

/**
 * 合规保留策略
 * @class   Worm
 */
class Worm extends BasicOss
{
    /**
     * 新建一条合规保留策略
     * @param   string  $name       Bucket名称(传空，表示从配置中获取)
     * @param   int     $day        保留天数(正整数)
     * @return  boolean
     */
    public function init(string $name = '', int $day)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_POST);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, '/?worm');
        $this->setData(self::OSS_BODY, Tools::arr2xml([
            'InitiateWormConfiguration' => [
                'RetentionPeriodInDays' => $day
            ]
            ], false));
        $res = $this->request(['worm']);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * 删除未锁定的合规保留策略
     * @param   string  $name       Bucket名称(传空，表示从配置中获取)
     * @return boolean
     */
    public function abort(string $name = '')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_DELETE);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?worm");
        $this->setData(self::OSS_URL_PARAM, '?worm');
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '204' ? true : false;
    }

    /**
     * 锁定合规保留策略
     * @param   string  $name       Bucket名称(传空，表示从配置中获取)
     * @param   string  $wormId     wormID
     * @return  boolean
     */
    public function complete(string $name = '', string $wormId)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_POST);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?wormId={$wormId}");
        $res = $this->request(['wormId' => $wormId]);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * 延长已锁定的合规保留策略对应Bucket中Object的保留天数
     * @param   string  $name       Bucket名称(传空，表示从配置中获取)
     * @param   string  $wormId     wormID
     * @param   int     $day        延长的天数
     * @return  boolean
     */
    public function exend(string $name = '', string $wormId, int $day)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_POST);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?wormId={$wormId}");
        $this->setData(self::OSS_BODY, Tools::arr2xml([
            'ExtendWormConfiguration' => [
                'RetentionPeriodInDays' => $day
            ]
        ], false));
        $res = $this->request(['wormId' => $wormId, 'wormExtend']);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * 获取指定存储空间（Bucket）的合规保留策略信息
     * @param   string  $name       Bucket名称(传空，表示从配置中获取)
     * @return  mixed
     */
    public function get(string $name = '', string $wormId)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_URL_PARAM, '?worm');
        $this->setData(self::OSS_RESOURCE, "/{$name}/?worm");
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }
}