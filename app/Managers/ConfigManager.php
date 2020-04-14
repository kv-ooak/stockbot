<?php

namespace App\Managers;

class ConfigManager {

    /**
     * 
     * @return boolean
     */
    public static function CacheEnable() {
        return true; // TODO: add control from manager screen
    }

    /**
     * 
     * @return int
     */
    public static function RecommendAvgVolumeRequire() {
        return 50000; // TODO: add control from manager screen
    }

}
