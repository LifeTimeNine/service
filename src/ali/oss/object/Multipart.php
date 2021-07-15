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
     * @param   string  $fileName           文件名称
     * @param   string  $cache              该Object被下载时的网页的缓存行为
     * @param   string  $disposition        该Object被下载时的名称
     * @param   string  $encodeing          该Object被下载时的内容编码格式
     * @param   int     $expires            过期时间，单位为毫秒
     * @param   boolean $overwrite          是否覆盖同名Object
     * @param   string  $storageClass       Object的存储类型
     * @return  mixed
     */
    public function init(string $name='',string $fileName,string $cache='',string $disposition='',string $encodeing='',int $expires=null,bool $overwrite=null,string $storageClass='')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());
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
     * 分块（Part）上传数据 (保存$partNumber和响应头中ETag)
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   int     $partNumber         Part标识
     * @param   string  $uploadId           上传唯一标识
     * @param   string  $data               数据
     * @return  mixed
     */
    public function part(string $name='',string $fileName,string $partNumber,string $uploadId,$data)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_URLENCODEED);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?partNumber={$partNumber}&uploadId={$uploadId}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}");
        $this->setData(self::OSS_BODY, $data);
        $res = $this->request([
            'partNumber' => $partNumber,
            'uploadId' => $uploadId
        ], false);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? Tools::header2arr($res) : false;
    }

    /**
     * 获取web端分块上传参数
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   int     $partNumber         Part标识
     * @param   string  $uploadId           上传唯一标识
     * @return  array
     */
    public function webParams(string $name='',string $fileName,string $partNumber,string $uploadId)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $endpoint = $this->getEndponit();
        $this->setData(self::OSS_ENDPOINT, $endpoint);
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_URLENCODEED);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?partNumber={$partNumber}&uploadId={$uploadId}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}");
        $header = $this->buildHeader(false);
        $url = "{$this->getProtocol()}{$name}.{$endpoint}{$this->getData(self::OSS_URL_PARAM)}";
        $filePath = "{$this->getProtocol()}{$name}.{$endpoint}/{$fileName}";
        return [
            'url' => $url,
            'header' => Tools::arrToKeyVal($header),
            'partNumber' => $partNumber,
            'filePath' => $filePath
        ];
    }

    /**
     * 从一个已存在的Object中拷贝数据来上传一个Part
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   int     $partNumber         Part标识
     * @param   string  $uploadId           上传唯一标识
     * @param   string  $sourceBucket       源Bucket名称(传空，表示当前Bucket)
     * @param   string  $sourceFileName     源文件名称
     * @param   int     $start              开始位置
     * @param   int     $end                结束位置
     * @return  mixed
     */
    public function copy(string $name='',string $fileName,string $partNumber,string $uploadId,string $sourceBucket='',string $sourceFileName,int $start = 0, int $end = null)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?partNumber={$partNumber}&uploadId={$uploadId}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}");
        if (empty($sourceBucket)) $sourceBucket = $name;
        $headerData = [
            'x-oss-copy-source'=> "/{$sourceBucket}/{$sourceFileName}",
        ];
        if ($start >= 0 && $end > 0) {
            $headerData['x-oss-copy-source-range'] = "bytes={$start}-{$end}";
        }
        $this->setData(self::OSS_OSS_HEADER, $headerData);
        $res = $this->request([
            'partNumber' => $partNumber,
            'uploadId' => $uploadId
        ]);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }

    /**
     * 完成分片上传
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   string  $uploadId           上传唯一标识
     * @param   array   $uploadData         上传数据[{partNumber} => {ETag},{partNumber} => {ETag}...]
     * @return  mixed
     */
    public function complete(string $name='',string $fileName,string $uploadId,array $uploadData)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_POST);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_URLENCODEED);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?uploadId={$uploadId}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}");
        $data = [];
        foreach($uploadData as $k => $v) $data[] = ['PartNumber' => $k, 'ETag' => $v];
        $this->setData(self::OSS_BODY, Tools::arr2xml([
            'CompleteMultipartUpload' => [
                'Part' => $data
            ]
            ], false));
        $res = $this->request([
            'uploadId' =>$uploadId
        ]);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }

    /**
     * 取消取消MultipartUpload事件并删除对应的Part数据
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   string  $uploadId           上传唯一标识
     * @return  mixed
     */
    public function abort(string $name='',string $fileName,string $uploadId)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_DELETE);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?uploadId={$uploadId}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}");
        $this->request([
            'uploadId' => $uploadId
        ]);
        return $this->getData(self::OSS_RESPONSE_CODE) == '204' ? true : false;
    }

    /**
     * 列举所有执行中的Multipart Upload事件
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $delimiter          对Object名字进行分组
     * @paarm   int     $max_uploads        此次返回Multipart Uploads事件的最大数目,默认为1000，max-uploads取值不能大于1000
     * @param   string  $key_marker         返回结果的起始位置
     * @param   string  $prefix             前缀
     * @return  mixed
     */
    public function list(string $name='',string $delimiter='',string $max_uploads='',string $key_marker='',string $prefix='')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?uploads");
        $this->setData(self::OSS_URL_PARAM, "?uploads");
        $query = [];
        if (!empty($delimiter)) $query['delimiter'] = $delimiter;
        if (!empty($max_uploads)) $query['max-uploads'] = $max_uploads;
        if (!empty($key_marker)) $query['key-marker'] = $key_marker;
        if (!empty($prefix)) $query['prefix'] = $prefix;
        $res = $this->request($query);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }

    /**
     * 指定Upload ID所属的所有已经上传成功Part
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   string  $uploadId           上传唯一标识
     * @return  mixed
     */
    public function listParts(string $name='',string $fileName,string $uploadId)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit());
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?uploadId={$uploadId}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}");
        $res = $this->request([
            'uploadId' => $uploadId
        ]);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }
}