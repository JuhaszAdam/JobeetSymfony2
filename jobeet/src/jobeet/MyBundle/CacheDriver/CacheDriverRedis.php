<?php

namespace jobeet\MyBundle\CacheDriver;

class CacheDriverRedis extends AbstractCacheDriver implements CacheDriverInterface
{
    /**
     * @var \Redis
     */
    private $redis;

    public function __construct(\Redis $redis, array $config)
    {
        $this->redis = $redis;
        $this->redis->connect($config['host'], $config['port']);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data, $ttl = null)
    {
        return $this->redis->setex($key, $this->getTtl($ttl), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->redis->del($key);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        return $this->redis->exists($key);
    }
}
