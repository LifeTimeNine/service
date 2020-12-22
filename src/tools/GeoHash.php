<?php
/*
 * @Description   GeoHash 算法类
 * @Author        lifetime
 * @Date          2020-12-22 16:41:01
 * @LastEditTime  2020-12-22 17:44:19
 * @LastEditors   lifetime
 */

namespace service\tools;

/**
 * GeoHash 算法
 */
class GeoHash
{
    /**
     * @description 获取经纬的Goehash
     * @access public
     * @param  Float    $lat    纬度
     * @param  Fload    $lng    经度
     * @param  Int      $len    长度
     * @return String   Geohash
     */
    public function encode($lat, $lng, $len = 12)
    {
        // 获取纬度编码
        $latCode = $this->getLatCode($lat, $len * 5 / 2);
        // 获取经度编码
        $lngCode = $this->getLngCode($lng, $len * 5 / 2);
        // 获取组合编码
        $groupCode = $this->getGroupCode($latCode, $lngCode, $len * 5 / 2);
        
        return $this->getCode($groupCode, $len);
    }
    /**
     * @description 获取纬度编码
     * @access protected
     * @param  Float    $lat    纬度
     * @param  Int      $lat    长度
     * @param  Float    $min    最小值
     * @param  Float    $max    最大值
     * @return String   code
     */
    protected function getLatCode($lat, $len, $min = -90, $max = 90)
    {
        $len -= 1;
        $m = ($min + $max) / 2;
        if ($lat > $min && $lat <= $m) {
            $code = 0;
            $max = $m;
        } else {
            $code = 1;
            $min = $m;
        }
        if ($len == 0) {
            return $code;
        } else {
            return $code . $this->getLngCode($lat, $len, $min, $max);
        }
    }
    /**
     * @description 获取经度编码
     * @access protected
     * @param  Float    $lng    经度
     * @param  Int      $lat    长度
     * @param  Float    $min    最小值
     * @param  Float    $max    最大值
     * @return String   code
     */
    protected function getLngCode($lng, $len, $min = -180, $max = 180)
    {
        $len -= 1;
        $m = ($min + $max) / 2;
        if ($lng > $min && $lng <= $m) {
            $code = 0;
            $max = $m;
        } else {
            $code = 1;
            $min = $m;
        }
        if ($len == 0) {
            return $code;
        } else {
            return $code . $this->getLngCode($lng, $len, $min, $max);
        }
    }
    /**
     * @description 获取组合Code
     * @access protected
     * @param  Float    $latCode    纬度编码
     * @param  Fload    $lngCode    经度编码
     * @param  Int      $len        长度
     * @return String
     */
    protected function getGroupCode($latCode, $lngCode, $len)
    {
        $groupCode = '';
        for($i = 0; $i < $len; $i++)
        {
            $groupCode .= "{$lngCode[$i]}{$latCode[$i]}";
        }
        return $groupCode;
    }
    /**
     * @description 获取编码
     * @access protected
     * @param  String   $groupCode  组合编码
     * @param  Int      $len        长度
     * @return String
     */
    protected function getCode($groupCode, $len)
    {
        // 字符集
        $str = "0123456789bcdefghjkmnpqrstuvwxyz";
        $code = '';
        for ($i=0; $i < $len; $i++) { 
            $base2str = substr($groupCode, $i * 5, 5);
            $base10str = bindec($base2str);
            $code .= $str[$base10str];
        }
        return $code;
    }
    /**
     * @根据经纬度计算距离
     * @access public
     * @param  Float    $lat1   纬度1
     * @param  Float    $lng1   经度1
     * @param  Float    $lat2   纬度2
     * @param  Float    $lng2   经度2
     * @return int
     */
    public function getDistance($lat1, $lng1, $lat2, $lng2) {
        //地球半径
        $R = 6378137;
        //将角度转为狐度
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        //结果
        $s = acos(cos($radLat1)*cos($radLat2)*cos($radLng1-$radLng2)+sin($radLat1)*sin($radLat2))*$R;
        //精度
        $s = round($s* 10000)/10000;
        return  round($s);
    }
}
