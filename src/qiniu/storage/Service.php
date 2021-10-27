<?php

namespace service\qiniu\storage;

use service\qiniu\basic\Storage;
use service\tools\Tools;

/**
 * Service相关操作
 * @calss   Service
 */
class Service extends Storage
{
    /**
     * 获取 Bucket 列表
     * @param   array   $tags   过滤空间的标签或标签值
     * @return
     */
    public function get(array $tags = [])
    {
        $this->initParam();
        $this->setData(self::S_METHOD, self::S_GET);
        $this->setData(self::S_HOST, self::S_HOST_UC);
        $this->setData(self::S_PATH, '/buckets');
        if (!empty($tags)) {
            $this->setData(self::S_QUERY, [
                'tagCondition' => $this->urlBase64(Tools::arrToUrl($tags))
            ]);
        }
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        return $this->request($this->bulidMgSign());
    }
}