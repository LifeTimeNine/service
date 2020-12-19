<?php
/*
 * @Description   常用工具类
 * @Author        lifetime
 * @Date          2020-12-17 16:43:54
 * @LastEditTime  2020-12-19 09:46:17
 * @LastEditors   lifetime
 */
namespace service\wechat\kernel;

use service\exceptions\CacheException;
use service\exceptions\InvalidResponseException;

class Tools
{

    /**
     * 缓存路径
     * @var null
     */
    public static $cache_path = null;

    /**
     * 缓存写入操作
     * @var array
     */
    public static $cache_callable = [
        'set' => null, // 写入缓存
        'get' => null, // 获取缓存
        'del' => null, // 删除缓存
        'put' => null, // 写入文件
    ];

    /**
     * CURL模拟网络请求
     * @param string $method 请求方法
     * @param string $url 请求方法
     * @param array $options 请求参数[headers,data,ssl_cer,ssl_key]
     * @return boolean|string
     * @throws Exception
     */
    public static function request($method, $url, $options = [])
    {
        $curl = curl_init();
        // GET参数设置
        if (!empty($options['query'])) {
            $url .= (stripos($url, '?') !== false ? '&' : '?') . http_build_query($options['query']);
        }
        // CURL头信息设置
        if (!empty($options['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        }
        // POST数据设置
        if (strtolower($method) === 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $options['data']);
        }
        // 证书文件设置
        if (!empty($options['ssl_cer'])) if (file_exists($options['ssl_cer'])) {
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLCERT, $options['ssl_cer']);
        } else throw new \Exception("Certificate files that do not exist. --- [ssl_cer]");
        // 证书文件设置
        if (!empty($options['ssl_key'])) if (file_exists($options['ssl_key'])) {
            curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLKEY, $options['ssl_key']);
        } else throw new \Exception("Certificate files that do not exist. --- [ssl_key]");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    }

    /**
     * 缓存配置与存储
     * @param string $name 缓存名称
     * @param string $value 缓存内容
     * @param int $expired 缓存时间(0表示永久缓存)
     * @return string
     * @throws CacheException
     */
    public static function setCache($name, $value = '', $expired = 3600)
    {
        if (is_callable(self::$cache_callable['set'])) {
            return call_user_func_array(self::$cache_callable['set'], func_get_args());
        }
        $file = self::_getCacheName($name);
        $data = ['name' => $name, 'value' => $value, 'expired' => time() + intval($expired)];
        if (!file_put_contents($file, serialize($data))) {
            throw new CacheException('local cache error.', '0');
        }
        return $file;
    }

    /**
     * 获取缓存内容
     * @param string $name 缓存名称
     * @return null|mixed
     */
    public static function getCache($name)
    {
        if (is_callable(self::$cache_callable['get'])) {
            return call_user_func_array(self::$cache_callable['get'], func_get_args());
        }
        $file = self::_getCacheName($name);
        if (file_exists($file) && ($content = file_get_contents($file))) {
            $data = unserialize($content);
            if (isset($data['expired']) && (intval($data['expired']) === 0 || intval($data['expired']) >= time())) {
                return $data['value'];
            }
            self::delCache($name);
        }
        return null;
    }

    /**
     * 移除缓存文件
     * @param string $name 缓存名称
     * @return boolean
     */
    public static function delCache($name)
    {
        if (is_callable(self::$cache_callable['del'])) {
            return call_user_func_array(self::$cache_callable['del'], func_get_args());
        }
        $file = self::_getCacheName($name);
        return file_exists($file) ? unlink($file) : true;
    }

    /**
     * 应用缓存目录
     * @param string $name
     * @return string
     */
    private static function _getCacheName($name)
    {
        if (empty(self::$cache_path)) {
            self::$cache_path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Cache' . DIRECTORY_SEPARATOR;
        }
        self::$cache_path = rtrim(self::$cache_path, '/\\') . DIRECTORY_SEPARATOR;
        file_exists(self::$cache_path) || mkdir(self::$cache_path, 0755, true);
        return self::$cache_path . $name;
    }

    /**
     * 数组转xml内容
     * @param array $data
     * @return null|string
     */
    public static function arr2json($data)
    {
        $json = json_encode(self::buildEnEmojiData($data), JSON_UNESCAPED_UNICODE);
        return $json === '[]' ? '{}' : $json;
    }
    /**
     * 解析JSON内容到数组
     * @param string $json
     * @return array
     * @throws InvalidResponseException
     */
    public static function json2arr($json)
    {
        $result = json_decode($json, true);
        if (empty($result)) {
            throw new InvalidResponseException('invalid response.', '0');
        }
        if (!empty($result['errcode']) && $result['errcode'] !== 0) {
            throw new InvalidResponseException($result['errmsg'], $result['errcode'], $result);
        }
        return $result;
    }

    /**
     * 数组对象Emoji编译处理
     * @param array $data
     * @return array
     */
    public static function buildEnEmojiData(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::buildEnEmojiData($value);
            } elseif (is_string($value)) {
                $data[$key] = self::emojiEncode($value);
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 数组对象Emoji反解析处理
     * @param array $data
     * @return array
     */
    public static function buildDeEmojiData(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::buildDeEmojiData($value);
            } elseif (is_string($value)) {
                $data[$key] = self::emojiDecode($value);
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Emoji原形转换为String
     * @param string $content
     * @return string
     */
    public static function emojiEncode($content)
    {
        return json_decode(preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($string) {
            return addslashes($string[0]);
        }, json_encode($content)));
    }

    /**
     * Emoji字符串转换为原形
     * @param string $content
     * @return string
     */
    public static function emojiDecode($content)
    {
        return json_decode(preg_replace_callback('/\\\\\\\\/i', function () {
            return '\\';
        }, json_encode($content)));
    }
}
