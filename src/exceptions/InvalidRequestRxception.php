<?php
/*
 * @Description   请求异常类
 * @Author        lifetime
 * @Date          2020-12-17 08:46:00
 * @LastEditTime  2020-12-17 08:46:50
 * @LastEditors   lifetime
 */

namespace service\exceptions;

class InvalidRequestException extends \Exception
{
    /**
     * @var array
     */
    public $raw = [];

    /**
     * InvalidRequestException constructor.
     * @param string $message
     * @param integer $code
     * @param array $raw
     */
    public function __construct($message, $code = 0, $raw = [])
    {
        parent::__construct($message, intval($code));
        $this->raw = $raw;
    }
}