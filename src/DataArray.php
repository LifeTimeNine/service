<?php
/*
 * @Description   DataArray
 * @Author        lifetime
 * @Date          2020-12-17 16:54:05
 * @LastEditTime  2020-12-17 16:55:46
 * @LastEditors   lifetime
 */

namespace service;

use ArrayAccess;

class DataArray implements ArrayAccess
{

    /**
     * 当前数据值
     * @var array
     */
    private $data = [];

    /**
     * Config constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->data = $options;
    }

    /**
     * 设置数据项值
     * @param string $offset
     * @param string|array|null|integer $value
     */
    public function set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    /**
     * 获取数据项参数
     * @param string|null $offset
     * @return array|string|null
     */
    public function get($offset = null)
    {
        return $this->offsetGet($offset);
    }

    /**
     * 合并数据到对象
     * @param array $data 需要合并的数据
     * @param bool $append 是否追加数据
     * @return array
     */
    public function merge(array $data, $append = false)
    {
        if ($append) {
            return $this->data = array_merge($this->data, $data);
        }
        return array_merge($this->data, $data);
    }

    /**
     * 设置数据项值
     * @param string $offset
     * @param string|array|null|integer $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * 判断数据Key是否存在
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * 清理数据项
     * @param string|null $offset
     */
    public function offsetUnset($offset = null)
    {
        if (is_null($offset)) {
            $this->data = [];
        } else {
            unset($this->data[$offset]);
        }
    }

    /**
     * 获取数据项参数
     * @param string|null $offset
     * @return array|string|null
     */
    public function offsetGet($offset = null)
    {
        if (is_null($offset)) {
            return $this->data;
        }
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}