<?php

namespace MyBundle\CacheDriver;

abstract class AbstractCacheDriver implements CacheDriverInterface
{
    /**
     * @var int
     */
    private $defaultTTL;

    /**
     * @param int $defaultTTL
     */
    public function setDefaultTTL($defaultTTL)
    {
        $this->defaultTTL = $defaultTTL;
    }

    /**
     * @param null $ttl
     * @return int|null
     */
    protected function getTtl($ttl = null)
    {
        return $ttl === null ? $this->defaultTTL : $ttl;
    }
}
