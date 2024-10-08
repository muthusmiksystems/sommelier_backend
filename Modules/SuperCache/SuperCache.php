<?php

namespace Modules\SuperCache;

use Cache;

class SuperCache
{
    public function cacheResponse($cacheName, $req = null)
    {
        return Cache::remember($cacheName, 3500, function () use ($req) {
            return $req;
        });
    }
}
