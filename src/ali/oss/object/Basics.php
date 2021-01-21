<?php

namespace service\ali\oss\object;

use service\ali\kernel\BasicOss;
use service\exceptions\InvalidArgumentException;
use service\tools\Tools;

/**
 * 基础操作
 * @class   Basics
 */
class Basics extends BasicOss
{
    /**
     * 上传文件(Object)
     * @param   string  $name           Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint       区域节点(传空，表示从配置中获取)
     * @param   string  $file           文件路径(绝对路径)
     * @param   string  $fileName       文件名称
     * @param   string  $acl            访问权限[private-私有，public-read-公共读，public-read-write-公共读写]
     * @param   string  $storageClass   储存类型[Standard（标准存储，默认值）IA（低频访问）Archive（归档存储）ColdArchive（冷归档存储）]
     * @param   string  $disposition    该Object被下载时的名称
     * @param   string  $cache          该Object被下载时网页的缓存行为
     * @param   int     $expires        过期时间
     * @param   string  $etag           ETag
     * @param   string  $encode         指定该Object被下载时的内容编码格式
     * @param   boolean $overwrite      是否禁止覆盖同名Object
     * @return  boolean
     */
    public function put(string $name = '', string $endpoint = '', string $file, string $fileName, string $acl='', string $storageClass='', string $disposition = '', string $cache = '', int $expires = null, string $etag = '', string $encode = '', bool $overwrite = null)
    {
        if (!file_exists($file)) throw new InvalidArgumentException("The file {$file} does not exist");
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));

        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_CONTENT_TYPE, Tools::getMimetype($fileName));
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}");
        $this->setData(self::OSS_BODY, file_get_contents($file));
        if (!empty($disposition)) $this->setData(self::OSS_DISPOSITION, $disposition);
        if (!empty($cache)) $this->setData(self::OSS_CACHE_CONTROL, $cache);
        if ($expires !== null) $this->setData(self::OSS_EXPIRES, $expires);
        if (!empty($etag)) $this->setData(self::OSS_ETAG, $etag);
        if (!empty($encode)) $this->setData(self::OSS_ENCODEING, $encode);
        $ossHeader = [];
        if (!empty($acl) && $this->checkAcl($acl)) $ossHeader[self::OSS_OBJECT_ACL] = $acl;
        if (!empty($storageClass) && $this->checkStorageClass($acl)) $ossHeader[self::OSS_STROAGE_CLASS] = $storageClass;
        if (!empty($overwrite)) $ossHeader[self::OSS_FORBID_OVERWIRTE] = $overwrite;
        if (empty($ossHeader)) $this->setData(self::OSS_OSS_HEADER, $ossHeader);
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * Web端上传数据 (url-请求地址，body-请求体(在最后插入file))
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   string  $success_redirect   上传成功后跳转的地址
     * @param   string  $success_status     未指定success_action_redirect表单域时，该参数指定了上传成功后返回给客户端的状态码
     * @param   string  $acl                访问权限[private-私有，public-read-公共读，public-read-write-公共读写]
     * @param   boolean $overwrite          是否覆盖同名Object
     * @param   array
     */
    public function webPut(string $name = '', string $endpoint = '', string $fileName,string $success_redirect = '',string $success_status = '200',string $acl = '', bool $overwrite = null)
    {
        $name = $this->getName($name);
        $endpoint = $this->getEndponit($endpoint);
        $time = time() + 3600;
        $date = date('Y-m-d', $time) . 'T' . date('H:i:s', $time) . '.000Z';
        $policyData = [
            'expiration' => $date,
            'conditions' => [
                ['bucket' => $name],
                ["content-length-range", 0, 1048576000],
            ]
        ];
        $policy = base64_encode(json_encode($policyData));
        $signature = $this->getSign($policy);
        $data = [
            'url' => "{$this->getProtocol()}{$name}.{$endpoint}",
            'body' => [
                'OSSAccessKeyId' => $this->config['accessKey_id'],
                'policy' => $policy,
                'Signature' => $signature,
                'x-oss-content-type' => Tools::getMimetype($fileName),
                'key' => $fileName,
            ]
        ];
        if (!empty($success_redirect)) $data['data']['success_action_redirect'] = $success_redirect;
        if (!empty($success_status)) $data['data']['success_action_status'] = $success_status;
        if (!empty($acl) && $this->checkAcl($acl)) $data['data'][self::OSS_OBJECT_ACL] = $acl;
        if (!empty($overwrite)) $data['data'][self::OSS_FORBID_OVERWIRTE] = $overwrite ? 'true' : 'false';
        return $data;
    }

    /**
     * 获取某个文件（Object）
     * @param   string  $name            Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint        区域节点(传空，表示从配置中获取)
     * @param   string  $fileName        文件名称
     * @param   string  $saveDir         保存文件夹(绝对路径,如果参数为空，则直接返回内容)
     * @param   string  $range           指定文件传输的范围
     * @param   string  $content_type    指定OSS返回请求的content-type头
     * @param   string  $content_lanuage 指定OSS返回请求的content-language头
     * @param   string  $expires         指定OSS返回请求的expires头
     * @param   string  $cache_control   指定OSS返回请求的cache-control头
     * @param   string  $disposition     指定OSS返回请求的content-disposition头
     * @param   string  $encodeing       指定OSS返回请求的content-encoding头
     * @return  mixed
     */
    public function get(string $name='', string $endpoint='', string $fileName, string $saveDir='', string $range='', string $content_type='', string $content_lanuage='', string $expires='', string $cache_control='', string $disposition='', string $encodeing = '')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_GET);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_PLAIN);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}");
        if (!empty($range)) $this->setData(self::OSS_RANGE, $range);
        if (!empty($content_type)) $this->setData(self::OSS_RESPONSE_CONTENT_TYPE, $content_type);
        if (!empty($content_lanuage)) $this->setData(self::OSS_RESPONSE_LANGUAGE, $content_lanuage);
        if (!empty($expires)) $this->setData(self::OSS_RESPONSE_EXPIRES, $expires);
        if (!empty($cache_control)) $this->setData(self::OSS_RESPONSE_CACHE, $cache_control);
        if (!empty($disposition)) $this->setData(self::OSS_RESPONSE_DISPONSITON, $disposition);
        if (!empty($encodeing)) $this->setData(self::OSS_RESPONSE_ENCODE, $encodeing);
        $res = $this->request([], false);
        if ($this->getData(self::OSS_RESPONSE_CODE) <> '200') return false;
        if (empty($saveDir)) return $res;
        if (!is_dir($saveDir)) mkdir($saveDir, 0777, true);
        file_put_contents("{$saveDir}/{$fileName}", $res);
        return "{$saveDir}/{$fileName}";
    }

    /**
     * 拷贝文件（Object）
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $sourceBucket       源Bucket(传空，表示当Bucket)
     * @param   string  $sourceFileName     源文件名称
     * @param   string  $putFileName        保存的文件名称
     * @param   string  $acl                访问权限[private-私有，public-read-公共读，public-read-write-公共读写]
     * @param   string  $storageClass       储存类型[Standard（标准存储，默认值）IA（低频访问）Archive（归档存储）ColdArchive（冷归档存储）]
     * @param   boolean $overwrite          是否禁止覆盖同名Object
     * @return  mixed
     */
    public function copy(string $name='',string $endpoint='',string $sourceBucket = '',string $sourceFileName,string $putFileName,string $acl='',string $storageClass='',string $overwrite=null)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_PUT);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_PLAIN);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$putFileName}");
        $this->setData(self::OSS_URL_PARAM, "/{$putFileName}");
        if (empty($sourceBucket)) $sourceBucket = $name;
        $ossHeader = ['x-oss-copy-source' => "/{$sourceBucket}/{$sourceFileName}"];
        if (!empty($acl) && $this->checkAcl($acl)) $ossHeader[self::OSS_OBJECT_ACL] = $acl;
        if (!empty($storageClass) && $this->checkStorageClass($storageClass)) $ossHeader[self::OSS_STROAGE_CLASS] = $storageClass;
        if (!empty($overwrite)) $ossHeader[self::OSS_FORBID_OVERWIRTE] = $overwrite ? 'true' : 'false';
        $this->setData(self::OSS_OSS_HEADER, $ossHeader);
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? $res : false;
    }

    /**
     * 以追加写的方式上传文件（Object）
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @param   string  $position           指定从何处进行追加
     * @param   string  $data               追加的内容
     * @param   string  $mimeType        文件类型
     * @param   string  $acl                访问权限[private-私有，public-read-公共读，public-read-write-公共读写]
     * @param   string  $storageClass       储存类型[Standard（标准存储，默认值）IA（低频访问）Archive（归档存储）ColdArchive（冷归档存储）]
     * @param   string  $cache              该Object被下载时网页的缓存行为
     * @param   string  $disposition        该Object被下载时的名称
     * @param   string  $encode             指定该Object被下载时的内容编码格式
     * @param   int     $expires            过期时间
     * @return  mixed
     */
    public function append(string $name='',string $endpoint='',string $fileName,string $position,string $data,string $mimeType, string $acl='',string $storageClass='',string $cache='',string $disposition='',string $encode='',string $expires='')
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_POST);
        if (!Tools::checkMimeType($mimeType)) throw new InvalidArgumentException("Unknown file type {$mimeType}");
        $this->setData(self::OSS_CONTENT_TYPE, $mimeType);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?append&position={$position}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}?append");
        $this->setData(self::OSS_BODY, $data);
        $ossHeader=[];
        if (!empty($acl) && $this->checkAcl($acl)) $ossHeader[self::OSS_OBJECT_ACL] = $acl;
        if (!empty($storageClass) && $this->checkStorageClass($storageClass)) $ossHeader[self::OSS_STROAGE_CLASS] = $storageClass;
        if (!empty($ossHeader)) $this->setData(self::OSS_OSS_HEADER, $ossHeader);
        if (!empty($cache)) $this->setData(self::OSS_CACHE_CONTROL, $cache);
        if (!empty($disposition)) $this->setData(self::OSS_RESPONSE_DISPONSITON, $disposition);
        if (!empty($encode)) $this->setData(self::OSS_RESPONSE_ENCODE, $encode);
        if (!empty($expires)) $this->setData(self::OSS_RESPONSE_EXPIRES, $expires);
        $this->request([
            'position' => $position
        ]);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * 删除某个文件（Object）
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
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}");
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? true : false;
    }

    /**
     * 删除同一个存储空间（Bucket）中的多个文件（Object）
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   array   $fileNameList       要删除的文件名称列表
     * @param   array   $quiet              简单相应模式
     * @return  mixed
     */
    public function deleteMultiple(string $name='',string $endpoint='',array $fileNameList,bool $quiet = false)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_POST);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/?delete");
        $this->setData(self::OSS_URL_PARAM, "?delete");
        foreach ($fileNameList as &$v) $v = ['Key' => $v];
        $this->setData(self::OSS_BODY, Tools::arr2xml([
            'Delete' => [
                'Quiet' => $quiet ? 'true' : 'false',
                'Object' => $fileNameList
            ]
        ], false));
        $res = $this->request();
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? ($quiet ? true : $res) : false;
    }

    /**
     * 获取某个文件（Object）的元信息
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @return  mixed
     */
    public function head(string $name='',string $endpoint='',string $fileName)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_HEAD);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}");
        $res = $this->request([], false);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? Tools::header2arr($res) : false;
    }

    /**
     * 获取一个文件（Object）的元数据信息，包括该Object的ETag、Size、LastModified信息
     * @param   string  $name               Bucket名称(传空，表示从配置中获取)
     * @param   string  $endpoint           区域节点(传空，表示从配置中获取)
     * @param   string  $fileName           文件名称
     * @return  mixed
     */
    public function getMeta(string $name='',string $endpoint='',string $fileName)
    {
        $name = $this->getName($name);
        $this->setData(self::OSS_BUCKET_NAME, $name);
        $this->setData(self::OSS_ENDPOINT, $this->getEndponit($endpoint));
        $this->setData(self::OSS_METHOD, self::OSS_HTTP_HEAD);
        $this->setData(self::OSS_CONTENT_TYPE, self::OSS_CONTENT_TYPE_XML);
        $this->setData(self::OSS_RESOURCE, "/{$name}/{$fileName}?objectMeta");
        $this->setData(self::OSS_URL_PARAM, "/{$fileName}?objectMeta");
        $res = $this->request([], false);
        return $this->getData(self::OSS_RESPONSE_CODE) == '200' ? Tools::header2arr($res) : false;
    }
}