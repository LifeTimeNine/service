<?php

namespace service\ali\oss;

use service\ali\kernel\BasicOss;

/**
 * 阿里云 OSS Service相关操作
 * @class Service
 */
class Service extends BasicOss
{
    /**
     * 返回所有储存空间
     * @param   string  $endpoint   地域节点 (传空，表示从配置中获取)
     * @param   string  $prexfix    前缀
     * @param   string  $marker     指定字母
     * @param   int     $max_key    指定最大数
     * @return  array
     */
    public function getService(string $endpoint = '', string $prexfix = '', string $marker = '', int $max_kays = 100)
    {
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_HTML);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_RESOURCE, '/');
        $result = $this->request([
            'prefix' => $prexfix,
            'marker' => $marker,
            'max-keys' => $max_kays
        ]);
        return $result;
    }
}