<?php

namespace Sywzj\TTOvertrue\Bridge;

use Psr\SimpleCache\CacheInterface;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\Cache\Simple\FilesystemCache;

trait CacheTrait
{
    /**
     * Doctrine\Common\Cache\Cache.
     */
    protected $cache;

    /**
     * 设置缓存驱动.
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * 获取缓存驱动.
     */
    public function getCache()
    {
        if ($this->cache) {
            return $this->cache;
        }

        return $this->cache = $this->createDefaultCache();
    }

    /**
     * @return \Symfony\Component\Cache\Simple\FilesystemCache
     */
    protected function createDefaultCache()
    {
        return new FilesystemCache();
    }
}
