<?php

namespace service\ali\oss\bucket;

use service\ali\kernel\BasicOss;
use service\exceptions\InvalidArgumentException;
use service\tools\Tools;

/**
 * 跨域资源共享
 * @class   Cors
 */
class Cors extends BasicOss
{
    /**
     * 设置跨域资源共享规则
     * @param   string  $name       Bucket名称(传空，表示从配置中获取)
     * @param   array   $data       共享策略[
     *          [
     *              'origin'(允许的跨域请求来源)=>[],
     *              'method'(允许的跨域请求方法,大写)=>[],
     *              'allowHeader'(允许的Headers)=>[],
     *              'exposeHeader'(暴露的Headers) => [],
     *              'cacheTime'(缓存时间) => 100,
     *              'vary'(是否返回Vary: Origin头) => false
     *          ]
     * ]
     */
    public function put(string $name = '',array $data)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?cors");
        $this->setData(self::OSS_URL_PARAM, "?cors");
        $corsRule = [];
        foreach ($data as $v)
        {
            $item = [];
            if (!empty($v['origin'])) $item['AllowedOrigin'] = $v['origin'];
            if (!empty($v['method'])) $item['AllowedMethod'] = $v['method'];
            if (!empty($v['allowHeader'])) $item['AllowedHeader'] = $v['allowHeader'];
            if (!empty($v['exposeHeader'])) $item['ExposeHeader'] = $v['exposeHeader'];
            if (!empty($v['cacheTime'])) $item['MaxAgeSeconds'] = $v['cacheTime'];
            if (!empty($v['vary'])) $item['ResponseVary'] = $v['vary'] ? 'true':'false';
            if (!empty($item)) $corsRule[] = $item;
        }
        $this->setData(self::OSS_BODY, Tools::arr2xml([
            'CORSConfiguration' => [
                'CORSRule' => $corsRule
            ]
        ], false));
        $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200';
    }

    /**
     * 获取跨域资源共享规则
     * @param   string  $name       Bucket名称(传空，表示从配置中获取)
     * @return  array
     */
    public function get(string $name = '')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?cors");
        $this->setData(self::OSS_URL_PARAM, "?cors");
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }

    /**
     * 删除跨域资源共享规则
     * @param   string  $name       Bucket名称(传空，表示从配置中获取)
     * @return  mixed
     */
    public function delete(string $name = '')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_DELETE);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?cors");
        $this->setData(self::OSS_URL_PARAM, "?cors");
        $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '204';
    }
}