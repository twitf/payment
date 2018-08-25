<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/21
 * Time: 22:29
 */

namespace twitf\Payment;

class Config implements \ArrayAccess
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function get($key = null, $default = null)
    {
        return ArrayHelp::get($this->config, $key, $default);
    }

    public function set($key, $value)
    {
        return ArrayHelp::set($this->config, $key, $value);
    }

    public function remove($key)
    {
        ArrayHelp::remove($this->config, $key);
    }

    public function exists($key)
    {
        return ArrayHelp::exists($this->config, $key);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->remove($offset);
        }
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->get($offset) : null;
    }
}
