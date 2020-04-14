<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Ticker
 *
 * @property integer $id
 * @property string $ticker
 * @property string $exchange
 * @property integer $outstanding
 * @property integer $listed
 * @property integer $treasury
 * @property integer $foreign_owned
 * @property integer $equity
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TickerRecommend[] $recommends
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TickerData[] $data
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TickerBot[] $bots
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TickerQuote[] $quotes
 */
class Ticker extends Model {

    protected $table = 'tickers';

    public function recommends() {
        return $this->hasMany(TickerRecommend::class);
    }

    public function data() {
        return $this->hasMany(TickerData::class);
    }

    public function bots() {
        return $this->hasMany(TickerBot::class);
    }

    public function quotes() {
        return $this->hasMany(TickerQuote::class);
    }

    public static function searchTicker($string = "", $limit = 0) {
        $ticker = new Ticker;
        if ($limit == 0) {
            return $ticker::where('ticker', 'LIKE', '%' . $string . '%')
                            ->get();
        } else {
            return $ticker::where('ticker', 'LIKE', '%' . $string . '%')
                            ->take($limit)
                            ->get();
        }
    }

    public static function checkTicker($string) {
        $ticker = new Ticker;
        return $ticker::where('ticker', $string)->first() !== null;
    }

    public static function getTickerList() {
        $ticker = new Ticker;
        return $ticker::select('ticker')
                        ->get();
    }

}
