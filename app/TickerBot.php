<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * App\TickerBot
 *
 * @property integer $id
 * @property integer $ticker_id
 * @property \App\Ticker $ticker
 * @property string $date
 * @property float $avg_volume_20
 * @property float $EMA9
 * @property float $MA20
 * @property float $MA42
 * @property float $MACD
 * @property float $MACDSignal
 * @property float $RSI
 * @property float $SAR
 * @property float $UpperBB
 * @property float $LowerBB
 * @property float $plusDI
 * @property float $minusDI
 * @property float $ADX
 * @property float $CCI
 * @method static \Illuminate\Database\Query\Builder|\App\TickerBot chooseFirst($ticker, $column)
 * @method static \Illuminate\Database\Query\Builder|\App\TickerBot choose($ticker, $column)
 * @method static \Illuminate\Database\Query\Builder|\App\TickerBot chooseEndAndPrev($ticker, $column, $date)
 */
class TickerBot extends Model {

    protected $table = 'ticker_bots';

    public function ticker() {
        return $this->belongsTo(Ticker::class);
    }

    public function scopeChooseFirst($query, $ticker, $column) {
        return $query->whereTicker($ticker)
                        //->orderBy('id', 'asc') // select from oldest date
                        ->orderBy('date', 'asc')
                        ->lists($column)
                        ->last();
    }

    public function scopeChoose($query, $ticker, $column) {
        return $query->whereTicker($ticker)
                        //->orderBy('id', 'asc') // select from oldest date
                        ->orderBy('date', 'asc')
                        ->lists($column)
                        ->all();
    }

    public function scopeChooseEndAndPrev($query, $ticker, $column, $date, $take = 2) {
        return $query->whereTicker($ticker)
                        ->where('date', '<=', $date)
                        ->orderBy('date', 'desc')
                        ->lists($column)
                        ->take($take)
                        ->reverse()
                        ->toArray();
    }

    public static function getDataByTicker($ticker) {
        $data = new TickerBot;
        return $data::where('ticker', $ticker)
                        //->orderBy('id', 'desc') // select from newest date
                        ->get();
    }

    public static function getLatestDate() {
        $data = new TickerBot;
        $temp = $data::select('date')
                ->distinct()
                ->max('date');
        return $temp;
    }

    public static function getLatestDates($take = 1) {
        $data = new TickerBot;
        $temp = $data::select('date')
                ->distinct()
                ->orderBy('date', 'desc')
                ->take($take)
                ->get();
        return $temp;
    }

    public static function checkDate($date) {
        $data = new TickerBot;
        return $data::where('date', $date)->first() !== null;
    }

}
