<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Managers\ConfigManager;

/**
 * App\TickerRecommend
 *
 * @property integer $id
 * @property integer $ticker_id
 * @property string $date
 * @property float $price
 * @property string $indicator
 * @property \App\Ticker $ticker
 * @property string $signal
 * @property float $avg_volume_20
 * @property float $net_buy
 * @property float $net_buy_value
 * @property string $strength
 * @property string $arrow
 */
class TickerRecommend extends Model {

    protected $table = 'ticker_recommends';
    public $timestamps = false;

    public function ticker() {
        return $this->belongsTo(Ticker::class);
    }

    public static function getDateList() {
        $ticker = new TickerRecommend;
        $data = $ticker::select('date')
                ->distinct()
                ->orderBy('date', 'desc')
                ->get();
        return $data;
    }

    public static function searchTicker($string = "", $symbol, $limit = 0) {
        $ticker = new TickerRecommend;
        if ($limit == 0) {
            $data = $ticker::where('ticker', $symbol)
                    ->where(function($query) use ($string) {
                        $query->where('indicator', 'LIKE', '%' . $string . '%')
                        ->orWhere('signal', 'LIKE', '%' . $string . '%');
                    })
                    ->get();
        } else {
            $data = $ticker::where('ticker', $symbol)
                    ->where(function($query) use ($string) {
                        $query->where('indicator', 'LIKE', '%' . $string . '%')
                        ->orWhere('signal', 'LIKE', '%' . $string . '%');
                    })
                    ->take($limit)
                    ->get();
        }
        return $data;
    }

    public static function searchTickerWithDate($string = "", $date, $limit = 0) {
        $ticker = new TickerRecommend;
        if ($limit == 0) {
            $data = $ticker::where('date', $date)
                    ->where('avg_volume_20', '>=', ConfigManager::RecommendAvgVolumeRequire())
                    ->where(function($query) use ($string) {
                        $query->where('ticker', 'LIKE', '%' . $string . '%')
                        ->orWhere('indicator', 'LIKE', '%' . $string . '%')
                        ->orWhere('signal', 'LIKE', '%' . $string . '%');
                    })
                    ->get();
        } else {
            $data = $ticker::where('date', $date)
                    ->where('avg_volume_20', '>=', ConfigManager::RecommendAvgVolumeRequire())
                    ->where(function($query) use ($string) {
                        $query->where('ticker', 'LIKE', '%' . $string . '%')
                        ->orWhere('indicator', 'LIKE', '%' . $string . '%')
                        ->orWhere('signal', 'LIKE', '%' . $string . '%');
                    })
                    ->take($limit)
                    ->get();
        }
        return $data;
    }

    public static function getLatestDates() {
        $ticker = new TickerRecommend;
        $data = $ticker::select('date')
                ->distinct()
                ->max('date');
        return $data;
    }

}
