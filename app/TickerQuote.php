<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TickerQuote
 *
 * @property integer $id
 * @property \App\Ticker $ticker
 * @property string $date
 * @property string $hour
 * @property float $bid
 * @property float $price
 * @property float $ask
 * @property integer $volume
 * @property integer $total_volume
 * @property string $status
 * @property float $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class TickerQuote extends Model {

    protected $table = 'ticker_quotes';

    public function ticker() {
        return $this->belongsTo(Ticker::class);
    }

    public static function getUncalculatedData() {
        return TickerQuote::where('value', null)
                        ->get();
    }

    public static function getDateList() {
        $ticker = new TickerQuote;
        $data = $ticker::select('date')
                ->distinct()
                ->orderBy('date', 'desc')
                ->get();
        return $data;
    }

    public static function getTickerList() {
        $ticker = new TickerQuote;
        $data = $ticker::select('ticker')
                ->distinct()
                ->orderBy('ticker', 'asc')
                ->get();
        return $data;
    }

    public static function getDataByTickerAndDate($ticker, $date, $string) {
        if ($string == '') {
            return TickerQuote::where('ticker', $ticker)
                            ->where('date', $date)
                            ->get();
        } else {
            return TickerQuote::where('ticker', $ticker)
                            ->where('date', $date)
                            ->where('status', 'LIKE', '%' . $string . '%')
                            ->get();
        }
    }

    public static function getLatestDates() {
        $ticker = new TickerQuote;
        $data = $ticker::select('date')
                ->distinct()
                ->max('date');
        return $data;
    }

    public static function netBuy($ticker, $date) {
        $quoteLine = TickerQuote::whereTicker($ticker)
                ->where('date', $date)
                ->get()
                ->toArray();
        $count = count($quoteLine);
        $buyVolume = null;
        $sellVolume = null;
        $buyValue = null;
        $sellValue = null;
        $netBuy = array();
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $date = $quoteLine[$i]['date'];
                $hour = $quoteLine[$i]['hour'];
                $price = $quoteLine[$i]['price'];
                $volume = $quoteLine[$i]['volume'];
                $status = $quoteLine[$i]['status'];
                $value = $quoteLine[$i]['value'];
                switch ($status) {
                    case 'BUY':
                        $buyVolume += $volume;
                        $buyValue += $value;
                        break;
                    case 'SELL':
                        $sellVolume += $volume;
                        $sellValue += $value;
                        break;
                    default:
                        break;
                }
            }
            if ($buyVolume != 0 || $sellVolume != 0) {
                $netBuy["vol"] = $buyVolume - $sellVolume;
                $netBuy["volPercent"] = $netBuy["vol"] * 100 / ($buyVolume + $sellVolume);
                $netBuy["value"] = $buyValue - $sellValue;
                $netBuy["valuePercent"] = $netBuy["value"] * 100 / ($buyValue + $sellValue);
            } else {
                $netBuy["vol"] = null;
                $netBuy["volPercent"] = null;
                $netBuy["value"] = null;
                $netBuy["valuePercent"] = null;
            }
        } else {
            $netBuy["vol"] = null;
            $netBuy["volPercent"] = null;
            $netBuy["value"] = null;
            $netBuy["valuePercent"] = null;
        }
        return $netBuy;
    }

}
