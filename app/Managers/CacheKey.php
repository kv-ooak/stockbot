<?php

namespace App\Managers;

class CacheKey {

    /**
     * 
     * @return string
     */
    public static function JobWarning() {
        return "job_warning";
    }

    /**
     * 
     * @return string
     */
    public static function JobWarningFlash() {
        return "job_warning_flash";
    }

    /**
     * 
     * @return string
     */
    public static function SessionFlashMessage() {
        return "flash_message";
    }

    /**
     * 
     * @return string
     */
    public static function TickerList() {
        return "ticker_list";
    }

    /**
     * 
     * @return string
     */
    public static function TickerRecommend() {
        return "ticker_recommend";
    }

    /**
     * 
     * @param type $ticker
     * @return type
     */
    public static function TickerData($ticker) {
        return "ticker_data_" . $ticker;
    }

    /**
     * 
     * @param type $ticker
     * @return type
     */
    public static function TickerBot($ticker) {
        return "ticker_bot_" . $ticker;
    }

    /**
     * 
     * @param type $ticker
     * @param type $from
     * @param type $to
     * @return type
     */
    public static function TickerChartData($ticker, $from, $to) {
        return "chart_data_" . $ticker . "_" . $from . "_" . $to;
    }

}
