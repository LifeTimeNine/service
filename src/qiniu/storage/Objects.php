<?php

namespace service\qiniu\storage;

use service\exceptions\InvalidArgumentException;
use service\qiniu\basic\Storage;
use service\tools\Tools;

/**
 * Object接口
 * @class   Object
 */
class Objects extends Storage
{
    /**
     * 修改文件的存储状态
     * @param   string  $bucketName 空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName   文件名
     * @param   boolean $status     是否禁用
     * @rerturn boolean
     */
    public function setStatus(string $bucketName = '', string $fileName, bool $status)
    {
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_RS);
        $status = $status ? 1 : 0;
        $this->setData(self::S_PATH, "/chstatus/{$this->urlBase64("{$this->getBucketName($bucketName)}:{$fileName}")}/status/{$status}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 更新文件生命周期
     * @param   string  $bucketName 空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName   文件名
     * @param   int     $day        保留天数(0表示取消生命周期)
     * @param   boolean
     */
    public function setLifecycle(string $bucketName = '', string $fileName, int $day)
    {
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_RS);
        $this->setData(self::S_PATH, "/deleteAfterDays/{$this->urlBase64("{$this->getBucketName($bucketName)}:{$fileName}")}/{$day}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 修改文件存储类型
     * @param   string  $bucketName 空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName   文件名
     * @param   int     $type       储存类型(0 表示标准存储；1 表示低频存储；2 表示归档存储)
     * @return  boolean
     */
    public function setStorageType(string $bucketName = '', string $fileName,int $type)
    {
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_RS);
        $this->checkStorageType($type);
        $this->setData(self::S_PATH, "/chtype/{$this->urlBase64("{$this->getBucketName($bucketName)}:{$fileName}")}/type/{$type}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 解冻归档存储文件
     * @param   string  $bucketName 空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName   文件名
     * @param   int     $day        解冻时长（可设置解冻有效期1～7天）
     * @return  boolean
     */
    public function thawArchive(string $bucketName = '', string $fileName,int $day)
    {
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_RS);
        $this->setData(self::S_PATH, "/restoreAr/{$this->urlBase64("{$this->getBucketName($bucketName)}:{$fileName}")}/freezeAfterDays/{$day}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 查询资源元信息
     * @param   string  $bucketName 空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName   文件名
     * @return  array
     */
    public function getMetaData(string $bucketName = '', string $fileName)
    {
        $this->setData(self::S_METHOD, self::S_GET);
        $this->setData(self::S_HOST, self::S_HOST_RS);
        $this->setData(self::S_PATH, "/stat/{$this->urlBase64("{$this->getBucketName($bucketName)}:{$fileName}")}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        return $this->request($this->bulidMgSign());
    }

    /**
     * 资源元信息修改
     * @param   string  $bucketName 空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName   文件名
     * @param   string  $mime       mime类型
     * @return  boolean
     */
    public function setMetaData(string $bucketName = '', string $fileName, string $mime)
    {
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_RS);
        if(!Tools::checkMimeType($mime)) throw new InvalidArgumentException("Unknown file mime {$mime}");
        $this->setData(self::S_PATH, "/chgm/{$this->urlBase64("{$this->getBucketName($bucketName)}:{$fileName}")}/mime/{$this->urlBase64($mime)}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 移动资源(可以用作重命名)
     * @param   string  $bucketName     源空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileNmae       源文件名称
     * @param   string  $toBucketName   目标空间名称(传空表示当前空间名)
     * @param   string  $toFileName     目标文件名称
     * @param   boolean $force          强制覆盖目标资源
     * @return  boolean
     */
    public function move(string $bucketName = '', string $fileName,string $toBucketName='',string $toFileName,bool $force=false)
    {
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_RS);
        $force = $force ? 'true' : 'false';
        $this->setData(self::S_PATH, "/move/{$this->urlBase64("{$this->getBucketName($bucketName)}:{$fileName}")}/{$this->urlBase64("{$this->getBucketName($toBucketName)}:{$toFileName}")}/force/{$force}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 复制资源
     * @param   string  $bucketName     源空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileNmae       源文件名称
     * @param   string  $toBucketName   目标空间名称(传空表示当前空间名)
     * @param   string  $toFileName     目标文件名称
     * @param   boolean $force          强制覆盖目标资源
     * @return  boolean
     */
    public function copy(string $bucketName = '', string $fileName,string $toBucketName='',string $toFileName,bool $force=false)
    {
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_RS);
        $force = $force ? 'true' : 'false';
        $this->setData(self::S_PATH, "/copy/{$this->urlBase64("{$this->getBucketName($bucketName)}:{$fileName}")}/{$this->urlBase64("{$this->getBucketName($toBucketName)}:{$toFileName}")}/force/{$force}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 删除资源
     * @param   string  $bucketName 空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName   文件名
     * @return  boolean
     */
    public function delete(string $bucketName = '', string $fileName)
    {
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_RS);
        $this->setData(self::S_PATH, "/delete/{$this->urlBase64("{$this->getBucketName($bucketName)}:{$fileName}")}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->request($this->bulidMgSign());
        return true;
    }

    /**
     * 资源列举
     * @param   string  $bucketName 空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $marker     上一次列举返回的位置标记，作为本次列举的起点信息
     * @param   int     $limit      本次列举的条目数，范围为1-1000
     * @param   string  $prefix     指定前缀
     * @param   string  $delimiter  指定目录分隔符，列出所有公共前缀（模拟列出目录效果
     * @return  array
     */
    public function list(string $bucketName='',string $marker='',int $limit=1000,string $prefix='',string $delimiter='')
    {
        $this->setData(self::S_METHOD, self::S_GET);
        $this->setData(self::S_HOST, self::S_HOST_RSF);
        $this->setData(self::S_PATH, "/list");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $query = ['bucket' => $this->getBucketName($bucketName)];
        if (!empty($marker)) $query['marker'] = $marker;
        if (!empty($limit)) $query['limit'] = $limit;
        if (!empty($prefix)) $query['prefix'] = $this->urlBase64($prefix);
        if (!empty($delimiter)) $query['delimiter'] = $this->urlBase64($delimiter);
        $this->setData(self::S_QUERY,$query);
        return $this->request($this->bulidMgSign());
    }

    /**
     * 批量操作
     * @param   array   $data   操作数据 (方法参数与对应的操作方法相同)[[e=>{操作方法}, {参数1} => {值1}，{参数2} => {值2}] 。。。]
     * @return  array
     */
    public function batch(array $data)
    {
        $body = $this->parseBatchData($data);
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, self::S_HOST_RS);
        $this->setData(self::S_PATH, "/batch");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->setData(self::S_BODY, $body);
        return $this->request($this->bulidMgSign());
    }

    /**
     * 上传文件
     * @param   string  $bucketName     空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName       文件名
     * @param   mixed   $data           数据
     * @param   int     $storageType    存储类型
     * @return  mixed
     */
    public function upload(string $bucketName='',string $fileName,$data,int $storageType=0)
    {
        $this->checkRegion($this->config['storage_region']);
        $region =  self::S_REGION_LIST[$this->config['storage_region']][2];
        $this->checkStorageType($storageType);
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, $region);
        $this->setData(self::S_UPLOAD_STARTEGY, [
            'scope' => "{$this->getBucketName($bucketName)}:{$fileName}",
            'deadline' => time() + 60,
            'returnBody' => Tools::arr2json([
                'name' => "$(fname)",
                'size' => "$(fsize)",
                'hash' => '$(etag)',
            ]),
            'fileType' => $storageType,
        ]);
        $upToken = $this->buildUploadSign();
        list($contentType, $body) = Tools::buildFormData([
            'token' => $upToken,
            'key' => $fileName,
            'fileName' => $fileName,
        ], $fileName, $data);
        $this->setData(self::S_CONTENT_TYPE, $contentType);
        $this->setData(self::S_BODY, $body);
        return $this->request('');
    }

    /**
     * 直传文件
     * @param   string  $bucketName     空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName       文件名
     * @param   int     $storageType    存储类型
     * @param   int     $expire         有效时间
     * @return  array
     */
    public function WebUpload(string $bucketName='',string $fileName,int $storageType=0, int $expire=3600)
    {
        $this->checkRegion($this->config['storage_region']);
        $region =  self::S_REGION_LIST[$this->config['storage_region']][2];
        $this->checkStorageType($storageType);
        $this->setData(self::S_UPLOAD_STARTEGY, [
            'scope' => "{$this->getBucketName($bucketName)}:{$fileName}",
            'deadline' => time() + $expire,
            'returnBody' => Tools::arr2json([
                'name' => "$(fname)",
                'size' => "$(fsize)",
                'hash' => '$(etag)',
            ]),
            'fileType' => $storageType,
        ]);
        $header = [
            self::S_CONTENT_TYPE => self::S_CONTENT_TYPE_FORMDATA
        ];
        $body = [
            'token' => $this->buildUploadSign(),
            'key' => "{$fileName}",
            'fileName' => $fileName
        ];
        return [
            'url' => "{$this->getProtocol()}{$region}",
            'header' => $this->formatWebData($header),
            'body' => $this->formatWebData($body),
            'fileFieldName' => 'file',
            'filePath' => "{$this->getProtocol()}{$this->getDomain()}/{$fileName}",
        ];
    }

    /**
     * 初始化分片上传任务
     * @param   string  $bucketName     空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName       文件名
     * @param   int     $storageType    存储类型
     * @return  array
     */
    public function initMultipart(string $bucketName='',string $fileName,int $storageType=0)
    {
        $this->checkRegion($this->config['storage_region']);
        $region =  self::S_REGION_LIST[$this->config['storage_region']][2];
        $this->checkStorageType($storageType);
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, $region);
        $this->setData(self::S_PATH, "/buckets/{$this->getBucketName($bucketName)}/objects/{$this->urlBase64($fileName)}/uploads");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_URLENCODE);
        $this->setData(self::S_UPLOAD_STARTEGY, [
            'scope' => "{$this->getBucketName($bucketName)}:{$fileName}",
            'deadline' => time() + 3600,
            'fileType' => $storageType,
        ]);
        $sign = $this->buildUploadSign();
        return $this->request("UpToken {$sign}");
    }

    /**
     * 分块上传数据
     * @param   string  $bucketName     空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName       文件名
     * @param   string  $uploadId       任务Id
     * @param   int     $partNumber     上传标记(0-1000,大小1MB-1GB)
     * @param   mixed   $data           长传的数据
     * @return  array
     */
    public function uploadPart(string $bucketName='',string $fileName,string $uploadId,int $partNumber,$data)
    {
        $this->checkRegion($this->config['storage_region']);
        $region =  self::S_REGION_LIST[$this->config['storage_region']][2];
        $this->setData(self::S_METHOD, self::S_PUT);
        $this->setData(self::S_HOST, $region);
        $this->setData(self::S_PATH, "/buckets/{$this->getBucketName($bucketName)}/objects/{$this->urlBase64($fileName)}/uploads/{$uploadId}/{$partNumber}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_OCTETSTREAM);
        $this->setData(self::S_BODY, $data);
        $this->setData(self::S_UPLOAD_STARTEGY, [
            'scope' => "{$this->getBucketName($bucketName)}:{$fileName}",
            'deadline' => time() + 3600,
        ]);
        return $this->request($this->buildUploadSign());
    }

    /**
     * web端分块上传数据
     * @param   string  $bucketName     空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName       文件名
     * @param   string  $uploadId       任务Id
     * @param   int     $partNumber     上传标记(0-1000,大小1MB-1GB)
     * @param   mixed   $data           长传的数据
     * @return  array
     */
    public function webPartParams(string $bucketName='',string $fileName,string $uploadId,int $partNumber)
    {
        $this->checkRegion($this->config['storage_region']);
        $region =  self::S_REGION_LIST[$this->config['storage_region']][2];
        $this->setData(self::S_METHOD, self::S_PUT);
        $this->setData(self::S_HOST, $region);
        $this->setData(self::S_PATH, "/buckets/{$this->getBucketName($bucketName)}/objects/{$this->urlBase64($fileName)}/uploads/{$uploadId}/{$partNumber}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_OCTETSTREAM);
        $this->setData(self::S_UPLOAD_STARTEGY, [
            'scope' => "{$this->getBucketName($bucketName)}:{$fileName}",
            'deadline' => time() + 3600,
        ]);
        $header = $this->bulidHeader($this->buildUploadSign());
        $url = "{$this->getProtocol()}{$this->getData(self::S_HOST)}{$this->getData(self::S_PATH)}";
        $filePath = "{$this->getProtocol()}{$this->getDomain()}/{$fileName}";
        return [
            'url' => $url,
            'header' => Tools::arrToKeyVal($header),
            'partNumber' => $partNumber,
            'filePath' => $filePath,
        ];
    }

    /**
     * 列举已经上传的分片
     * @param   string  $bucketName     空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName       文件名
     * @param   string  $uploadId       任务Id
     * @param   int     $max_parts      响应中的最大 Part 数目
     * @param   int     $marker         指定列举的起始位置
     * @return  array
     */
    public function partList(string $bucketName='',string $fileName,string $uploadId,int $max_parts=1000,string $marker='')
    {
        $this->checkRegion($this->config['storage_region']);
        $region =  self::S_REGION_LIST[$this->config['storage_region']][2];
        $this->setData(self::S_METHOD, self::S_GET);
        $this->setData(self::S_HOST, $region);
        $this->setData(self::S_PATH, "/buckets/{$this->getBucketName($bucketName)}/objects/{$this->urlBase64($fileName)}/uploads/{$uploadId}");
        $query = ['max-parts' => $max_parts];
        if (!empty($marker)) $query['part-number-marker'] = $marker;
        $this->setData(self::S_QUERY, $query);
        $this->setData(self::S_UPLOAD_STARTEGY, [
            'scope' => "{$this->getBucketName($bucketName)}:{$fileName}",
            'deadline' => time() + 10,
        ]);
        return $this->request($this->buildUploadSign());
    }

    /**
     * 完成分片上传
     * @param   string  $bucketName     空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName       文件名
     * @param   string  $uploadId       任务Id
     * @param   array   $data           参数[{partNumber} => {etag}...]
     * @return  array
     */
    public function completePart(string $bucketName='',string $fileName,string $uploadId,array $data)
    {
        $this->checkRegion($this->config['storage_region']);
        $region =  self::S_REGION_LIST[$this->config['storage_region']][2];
        $this->setData(self::S_METHOD, self::S_POST);
        $this->setData(self::S_HOST, $region);
        $this->setData(self::S_PATH, "/buckets/{$this->getBucketName($bucketName)}/objects/{$this->urlBase64($fileName)}/uploads/{$uploadId}");
        $this->setData(self::S_CONTENT_TYPE, self::S_CONTENT_TYPE_JOSN);
        $parts = [];
        foreach($data as $k => $v) $parts[] = ['etag' => $v, 'partNumber' => $k];
        $this->setData(self::S_BODY, Tools::arr2json([
            'parts' => $parts,
            'fname' => $fileName
        ]));
        $this->setData(self::S_UPLOAD_STARTEGY, [
            'scope' => "{$this->getBucketName($bucketName)}:{$fileName}",
            'deadline' => time() + 10,
        ]);
        return $this->request($this->buildUploadSign());
    }

    /**
     * 终止分片上传
     * @param   string  $bucketName     空间名称(传空表示从配置中获取[storage_bucketName])
     * @param   string  $fileName       文件名
     * @param   string  $uploadId       任务Id
     */
    public function stopPart(string $bucketName='',string $fileName,string $uploadId)
    {
        $this->checkRegion($this->config['storage_region']);
        $region =  self::S_REGION_LIST[$this->config['storage_region']][2];
        $this->setData(self::S_METHOD, self::S_DELETE);
        $this->setData(self::S_HOST, $region);
        $this->setData(self::S_PATH, "/buckets/{$this->getBucketName($bucketName)}/objects/{$this->urlBase64($fileName)}/uploads/{$uploadId}");
        $this->setData(self::S_UPLOAD_STARTEGY, [
            'scope' => "{$this->getBucketName($bucketName)}:{$fileName}",
            'deadline' => time() + 10,
        ]);
        $this->request($this->buildUploadSign());
        return true;
    }

    /**
     * 解析批量操作数据
     * @param   array   $data
     * @rerun   array
     */
    private function parseBatchData($data)
    {
        $parseData = [];
        foreach ($data as $k => $v) {
            $bucketName = empty($v['bucketName']) ? $this->getBucketName('') : $v['bucketName'];
            if (empty($v['fileName'])) throw new InvalidArgumentException("Missing the required parameter [bucketName] from index-{$k}");
            $fileName = $v['fileName'];
            if (empty($v['e'])) throw new InvalidArgumentException("Missing the required parameter [e] from index-{$k}");
            switch ($v['e']) {
                case 'getMeteData':
                    $parseData[] = "/stat/{$this->urlBase64("{$bucketName}:{$fileName}")}";
                    break;
                case 'copy':
                    $toBucketName = empty($v['toBucketName']) ? $this->getBucketName('') : $v['toBucketName'];
                    if (empty($v['toFileName'])) throw new InvalidArgumentException("Missing the required parameter [toFileName from index-{$k}");
                    $toFileName = $v['toFileName'];
                    $force = empty($v['force']) ? 'false' : 'true';
                    $parseData[] = "/copy/{$this->urlBase64("{$bucketName}:{$fileName}")}/{$this->urlBase64("{$toBucketName}:{$toFileName}")}/force/{$force}";
                    break;
                case 'move':
                    $toBucketName = empty($v['toBucketName']) ? $this->getBucketName('') : $v['toBucketName'];
                    if (empty($v['toFileName'])) throw new InvalidArgumentException("Missing the required parameter [toFileName from index-{$k}");
                    $toFileName = $v['toFileName'];
                    $force = empty($v['force']) ? 'false' : 'true';
                    $parseData[] = "/move/{$this->urlBase64("{$bucketName}:{$fileName}")}/{$this->urlBase64("{$toBucketName}:{$toFileName}")}/force/{$force}";
                    break;
                case 'delete':
                    $parseData[] = "/delete/{$this->urlBase64("{$bucketName}:{$fileName}")}";
                    break;
                case 'thaw':
                    if (empty($v['day'])) throw new InvalidArgumentException("Missing the required parameter [day] from index-{$k}");
                    $parseData[] = "/restoreAr/{$this->urlBase64("{$bucketName}:{$fileName}")}/freezeAfterDays/{$v['day']}";
                    break;
            }
        }
        return "op=" . implode('&op=', $parseData);
    }

    /**
     * 格式化web参数
     * @param   array   $data
     * @return  array
     */
    private function formatWebData($data)
    {
        $webData = [];
        foreach($data as $k => $v)
        {
            $webData[] = ['key' => $k, 'value' => $v];
        }
        return $webData;
    }

    /**
     * 验证储存类型
     * @param   int     $type   储存类型
     * @return  boolean
     */
    private function checkStorageType($type)
    {
        if (!in_array($type, [0,1,2])) throw new InvalidArgumentException("Unknown storage type {$type}");
        return true;
    }
}
