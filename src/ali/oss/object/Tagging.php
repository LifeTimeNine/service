<?php

namespace service\ali\oss\object;

use service\ali\kernel\BasicOss;
use service\tools\Tools;

/**
 * 标签
 * @class   Tagging
 */
class Tagging extends BasicOss
{
    /**
     * 设置或更新对象（Object）的标签（Tagging）信息
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   array   $tagData            标签数据[{key}=>{value}]
     * @return  mixed
     */
    public function put(string $name='',string $endpoint='',string $fileName,array $tagData)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?tagging");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}?tagging");
        $body = [];
        foreach($tagData as $k => $v) $body[] = ['Key' => $k, 'Value' => $v];
        $this->setData(self::OSS_BODY, Tools::arr2xml([
            'Tagging' => [
                'TagSet' => [
                    'Tag' => $body
                ]
            ]
        ], false));
        $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * 获取对象（Object）的标签（Tagging）信息
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @return  mixed
     */
    public function get(string $name='',string $endpoint='',string $fileName)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?tagging");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}?tagging");
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }

    /**
     * 删除指定对象（Object）的标签（Tagging）信息
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @return  mixed
     */
    public function delete(string $name='',string $endpoint='',string $fileName)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_DELETE);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?tagging");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}?tagging");
        $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }
}