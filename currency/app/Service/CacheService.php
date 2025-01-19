<?php

namespace App\Service;

use Closure;

class CacheService
{
    /**
     * Cache time 3 minutes
     * 
     * @var int 
     */
    protected int $cacheTime = 60 * 3;

    /**
     * Provider service cache 
     * 
     * @var string 
     */
    protected string $provider = 'redis';

    /**
     * @param string $key
     * @param Closure
     * 
     * @return mixed
     */
    public function fromCache(string $key, Closure $resolve): mixed
    {
        $redis = app($this->provider);

        if ($redis->exists($key)) {
            return json_decode($redis->get($key), true);
        }

        $result = $resolve();

        $redis->set($key, json_encode($result));
        $redis->expire($key, $this->cacheTime);

        return $result;
    }
}