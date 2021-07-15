<?php
/*
 * @Description   参数异常类
 * @Author        lifetime
 * @Date          2020-12-13 22:37:17
 * @LastEditTime  2020-12-13 22:49:13
 * @LastEditors   lifetime
 */

namespace service\exceptions;

class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * @var array
     */
    public $raw = [];

    /**
     * InvalidArgumentException constructor.
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