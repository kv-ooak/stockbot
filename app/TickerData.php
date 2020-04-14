<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TickerData
 *
 * @property integer $id
 * @property integer $ticker_id
 * @property \App\Ticker $ticker
 * @property string $date
 * @property float $open
 * @property float $high
 * @property float $low
 * @property float $close
 * @property integer $volume
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\TickerData choose($ticker, $column)
 * @method static \Illuminate\Database\Query\Builder|\App\TickerData chooseRange($ticker, $column, $from, $to)
 * @method static \Illuminate\Database\Query\Builder|\App\TickerData chooseEndAndPrev($ticker, $column, $date)
 */
class TickerData extends Model {

    protected $table = 'ticker_data';

    public function ticker() {
        return $this->belongsTo(Ticker::class);
    }

    public function scopeChoose($query, $ticker, $column) {
        return $query->whereTicker($ticker)
                        //->orderBy('id', 'desc') // select from oldest date
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

    public function scopeChooseRange($query, $ticker, $column, $from, $to) {
        return $query->whereTicker($ticker)
                        ->whereBetween('date', array($from, $to))
                        //->orderBy('id', 'desc') // select from oldest date
                        ->orderBy('date', 'asc')
                        ->lists($column)
                        ->all();
    }

    public static function getDataByTicker($ticker) {
        $data = new TickerData;
        return $data::where('ticker', $ticker)
                        //->orderBy('id', 'asc') // select from newest date
                        ->get();
    }

}
