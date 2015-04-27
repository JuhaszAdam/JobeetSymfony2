<?php

namespace MyBundle\CacheDriver;

interface CacheDriverInterface
{
    /**
     * @param string $key
     * @return mixed|null
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed  $data
     * @param int    $ttl
     * @return array|bool
     */
    public function set($key, $data, $ttl = null);

    /**
     * @param string $key
     * @return bool|string[]
     */
    public function delete($key);

    /**
     * @param string $key
     * @return bool
     */
    public function exists($key);
}
