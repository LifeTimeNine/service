<?php
/*
 * @Description   返回数据异常类
 * @Author        lifetime
 * @Date          2020-12-16 16:46:44
 * @LastEditTime  2020-12-23 10:20:23
 * @LastEditors   lifetime
 */
namespace service\exceptions;

class InvalidResponseException extends \Exception
{
    /**
     * @var array
     */
    public $raw = [];

    /**
     * InvalidResponseException constructor.
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
