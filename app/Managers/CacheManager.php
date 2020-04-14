<?php

namespace App\Managers;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CacheManager {

    const time5sec = 5;
    const time30sec = 30;
    const time1min = 60;
    const time5min = 300;
    const time30min = 1800;
    const time60min = 3600;

    /**
     * 
     * @param type $cache_key
     * @param type $value
     * @param type $time
     */
    public static function AddNew($cache_key, $value, $time) {
        $expiresAt = Carbon::now()->addSecond($time);
        Cache::put($cache_key, $value, $expiresAt);
    }

    /**
     * 
     * @param type $cache_key
     */
    public static function Remove($cache_key) {
        Cache::forget($cache_key);
    }

}
