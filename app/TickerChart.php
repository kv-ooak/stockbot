<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TickerChart
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $symbol
 * @property string $resolution
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class TickerChart extends Model {

    protected $table = 'ticker_charts';

    public static function getChartList($user_id = 0) {
        $data = new TickerChart;
        if ($user_id > 0) {
            return $data::select('id', 'name', 'symbol', 'resolution', 'updated_at as timestamp')
                            ->where('user_id', $user_id)
                            ->orderBy('updated_at', 'desc')
                            ->get();
        } else {
            return $data::select('id', 'name', 'symbol', 'resolution', 'updated_at as timestamp')
                            ->orderBy('updated_at', 'desc')
                            ->get();
        }
    }

    public static function getChartById($id, $user_id = 0) {
        $data = new TickerChart;
        if ($user_id > 0) {
            return $data::select('id', 'name', 'symbol', 'resolution', 'updated_at as timestamp', 'content')
                            ->where('id', $id)
                            ->where('user_id', $user_id)
                            ->get();
        } else {
            return $data::select('id', 'name', 'symbol', 'resolution', 'updated_at as timestamp', 'content')
                            ->where('id', $id)
                            ->get();
        }
    }

    public static function getChart($id, $user_id) {
        $data = new TickerChart;
        return $data::where('user_id', $user_id)
                        ->where('id', $id)
                        ->first();
    }

}
