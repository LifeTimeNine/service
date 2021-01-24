<?php

namespace service\ali\oss\bucket;

use service\ali\kernel\BasicOss;
use service\tools\Tools;

/**
 * 防盗链
 * @class   Referer
 */
class Referer extends BasicOss
{
    /**
     * 设置存储空间(Bucket)的防盗链(Referer)----测试中
     * @param   string  $name           Bucket名称(传空，表示从配置中获取)
     * @param   string  $emptyRefere    是否允许Referer字段为空的请求访问
     * @param   array   $refererList    Referer访问白名单
     * @return  boolean
     */
    public function put(string $name = '', bool $emptyReferer = true, array $refererList = [])
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?referer");
        $this->setData(self::OSS_URL_PARAM, '?referer');

        $referer = [];
        foreach($refererList as $v) $referer[] = ['Referer' => $v];

        $this->setData(self::OSS_BODY, Tools::arr2xml([
            'RefererConfiguration' => [
                'AllowEmptyReferer' => $emptyReferer ? 'true' : 'false',
                'RefererList' => $referer
            ]
        ], false));
        $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * 查看存储空间（Bucket）的防盗链（Referer）相关配置
     * @param   string  $name           Bucket名称(传空，表示从配置中获取)
     * @return  mixed
     */
    public function get(string $name = '')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?referer");
        $this->setData(self::OSS_URL_PARAM, '?referer');

        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }
}