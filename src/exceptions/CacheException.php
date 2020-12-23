<?php
/*
 * @Description   缓存异常类
 * @Author        lifetime
 * @Date          2020-12-18 21:44:16
 * @LastEditTime  2020-12-23 10:20:34
 * @LastEditors   lifetime
 */
namespace service\exceptions;

class CacheException extends \Exception
{
    /**
     * @var array
     */
    public $raw = [];

    /**
     * CacheException constructor.
     * @param string $message
     * @param integer $code
     * @param array $raw
     */
    public function __construct($message, $code = 0, $raw = [])
    {
        parent::__construct($message, intval($code));
        $this->raw = $raw;
    }

    public function getRaw()
    {
        return $this->raw;
    }
}
