<?php
/*
 * @Description   工具类
 * @Author        lifetime
 * @Date          2020-12-22 14:41:40
 * @LastEditTime  2020-12-22 16:48:06
 * @LastEditors   lifetime
 */

namespace service\tools;

use service\exceptions\InvalidArgumentException;
use service\exceptions\InvalidResponseException;

/**
 * 常用工具类
 */
class Tools
{
    /**
     * CURL模拟网络请求
     * @param string $method 请求方法
     * @param string $url 请求方法
     * @param array $options 请求参数[headers,data,ssl_cer,ssl_key]
     * @return string
     * @throws Exception
     */
    public static function request(string $method, string $url, array $options = [])
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
            throw new InvalidArgumentException('invalid response.', '0');
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

    /**
     * 产生随机字符串
     * @param int $length 指定字符长度
     * @param string $str 字符串前缀
     * @return string
     */
    public static function createNoncestr($length = 32, $str = "")
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 数组转url
     * @param   array   $arr
     * @return string
     */
    public static function arrToUrl($arr)
    {
        $buff = [];
        foreach ($arr as $key => $value) {
            if ($key <> "sign" && $key <> ''&& !is_array($value)) {
                $buff[] = "{$key}={$value}";
            }
        }
        return implode('&', $buff);
    }

    /**
     * 数组转XML内容
     * @param array $data
     * @return string
     */
    public static function arr2xml($data)
    {
        return "<xml>" . self::_arr2xml($data) . "</xml>";
    }

    /**
     * XML内容生成
     * @param array $data 数据
     * @param string $content
     * @return string
     */
    private static function _arr2xml($data, $content = '')
    {
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = 'item';
            $content .= "<{$key}>";
            if (is_array($val) || is_object($val)) {
                $content .= self::_arr2xml($val);
            } elseif (is_string($val)) {
                $content .= '<![CDATA[' . preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $val) . ']]>';
            } else {
                $content .= $val;
            }
            $content .= "</{$key}>";
        }
        return $content;
    }

    /**
     * 解析XML内容到数组
     * @param string $xml
     * @return array
     */
    public static function xml2arr($xml)
    {
        $entity = libxml_disable_entity_loader(true);
        $data = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        libxml_disable_entity_loader($entity);
        return json_decode(json_encode($data), true);
    }

    /**
     * 根据出生年月日获取年龄
     * @param  string   $birthday   出生如期YYYY-mm-dd
     * @return  int 年龄
     */
    public static function getAge(string $birth)
    {
        list($birthYear, $birthMonth, $birthDay) = explode('-', $birth);
        list($currentYear, $currentMonth, $currentDay) = explode('-', date('Y-m-d'));
        $age = $currentYear - $birthYear - 1;
        if ($currentMonth > $birthMonth || $currentMonth == $birthMonth && $currentDay >= $birthDay)
            $age++;
        return $age;
    }

    /**
     * 生成订单编号 (前缀+日期+用户id+随机数)
     * @param   string     $uid        用户id
     * @param   string  $prefix     前缀
     * @param   int     $randLen    随机数长度
     * @return  string
     */
    public static function createOrderSn(string $uid = null, string $prefix = null, int $randLen = null)
    {
        if ($randLen === null) $randLen = 3;
        $date = date('YmdHis');
        $rand = '';
        while ($randLen-- > 0) $rand .= rand(0, 9);
        return "$prefix$date$uid$rand";
    }

    /**
     * 判断字符串是否是json字符串
     * @param   string  $string     要检测的字符串
     * @return  bool    判断结果
     */
    public static function isJson(string $string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 获取毫秒级时间戳
     * @return  float
     */
    public static function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    /**
     * 获取某个月的天数
     * @param  string   $date       日期 （Y-m）
     * @return  int     天数
     */
    public static function getMonthDay($date)
    {
        return date('t', strtotime($date));
    }

    /**
     * 删除文件夹及其所有子文件
     * @param  string   $path   文件目录
     */
    public static function deldir($path)
    {
        //如果是目录则继续
        if (is_dir($path)) {
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach ($p as $val) {
                //排除目录中的.和..
                if ($val != "." && $val != "..") {
                    //如果是目录则递归子目录，继续操作
                    if (is_dir($path . '/' . $val)) {
                        //子目录中操作删除文件夹和文件
                        self::deldir($path . '/' . $val);
                        //目录清空后删除空文件夹
                        @rmdir($path . '/' . $val);
                    } else {
                        //如果是文件直接删除
                        unlink($path . '/' . $val);
                    }
                }
            }
            @rmdir($path);
        }
    }
}
