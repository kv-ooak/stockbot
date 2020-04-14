<?php

namespace App\Managers;

use Illuminate\Support\Facades\Log;
use App\Ticker;
use App\TickerRecommend;

class BotManager {

    /**
     * 
     * @param type $indicator
     * @param type $signal
     * @param type $ticker
     * @param type $date
     * @param type $avgVol
     * @param type $netBuy
     */
    public static function recordToRecommend($indicator, $signal, $ticker, $date, $avgVol, $netBuy, $close) {
        $id = Ticker::whereTicker($ticker)->lists('id')->first();
        $tickers = Ticker::find($id);
        $recommend = new TickerRecommend;
        $recommend->indicator = $indicator;
        $recommend->date = $date;
        $recommend->ticker = $ticker;
        $recommend->price = $close;
        $recommend->signal = $signal;
        $recommend->avg_volume_20 = $avgVol;
        $recommend->net_buy = $netBuy["vol"];
        $recommend->net_buy_value = $netBuy["value"];
        switch (substr($signal, -3)):
            case "BUY":
                $recommend->strength = 'POS';
                $recommend->arrow = "&#8679;";
                break;
            case "ELL":
                $recommend->strength = 'NEG';
                $recommend->arrow = "&#8681;";
                break;
        endswitch;

        switch (substr($signal, 5)):
            case "RESISTANCE":
            case "BREAKOUT UP":
                $recommend->strength = 'POS';
                $recommend->arrow = "&#8660;";
                break;
            case "SUPPORT":
            case "BREAKOUT DOWN":
                $recommend->strength = 'NEG';
                $recommend->arrow = "&#8660;";
                break;
        endswitch;

        switch ($signal):
            case "BOUNCE":
            case "FOLLOW UP":
                $recommend->strength = 'POS';
                $recommend->arrow = "&#8679;";
                break;
            case "FALLBACK":
            case "FOLLOW DOWN":
                $recommend->strength = 'NEG';
                $recommend->arrow = "&#8681;";
                break;
        endswitch;

        return $tickers->recommends()->save($recommend);
    }

    public static function isAbove($lineA, $lineB) {
        return (end($lineA) > end($lineB));
    }

    public static function crossOver(array $lineA, array $lineB) {
        return (end($lineA) >= end($lineB) && prev($lineA) < prev($lineB));
    }

    public static function crossUnder(array $lineA, array $lineB) {
        return (end($lineA) <= end($lineB) && prev($lineA) > prev($lineB));
    }

    public static function positiveConverge(array $price, array $indicator) {
        return (end($price) > prev($price) && end($indicator) >= prev($indicator));
    }

    public static function negativeConverge(array $price, array $indicator) {
        return (end($price) < prev($price) && end($indicator) <= prev($indicator));
    }

    public static function positiveDiverge(array $price, array $indicator) {
        return (end($price) <= prev($price) && end($indicator) >= prev($indicator));
    }

    public static function negativeDiverge(array $price, array $indicator) {
        return (end($price) >= prev($price) && end($indicator) <= prev($indicator));
    }

    public static function testSupport(array $price, array $indicator) {
        return (
                end($price) > end($indicator) && end($price) - end($indicator) <= end($price) * 0.02 &&
                end($price) < prev($price) && end($indicator) >= prev($indicator)
                );
    }

    public static function testResistance(array $price, array $indicator) {
        return (
                end($price) < end($indicator) && end($indicator) - end($price) <= end($price) * 0.02 &&
                end($price) > prev($price) && end($indicator) <= prev($indicator)
                );
    }

    public static function BreakoutUp(array $price, array $indicator) {
        return (
                end($price) > prev($price) && end($price) > end($indicator) && end($price) - end($indicator) <= end($price) * 0.02 &&
                prev($price) <= prev($price) && prev($indicator) >= prev($indicator)
                );
    }

    public static function BreakoutDown(array $price, array $indicator) {
        return (
                end($price) < prev($price) && end($price) < end($indicator) && end($indicator) - end($price) <= end($price) * 0.02 &&
                prev($price) >= prev($price) && prev($indicator) <= prev($indicator)
                );
    }

    public static function compareTrend(array $price, array $indicator) {
        return (end($price) - prev($price)) * (end($indicator) - prev($indicator)) >= 0;
    }

    /**
     * 
     * @param array $ADX
     * @param array $line_20
     * @param array $plusDI
     * @param array $minusDI
     * @param type $ticker
     * @param type $date
     * @param type $avgVol
     * @param type $netBuy
     * @param type $close
     * @return type
     */
    public static function testADX(array $ADX, array $line_20, array $plusDI, array $minusDI, $ticker, $date, $avgVol, $netBuy, $close) {
        if (BotManager::crossOver($plusDI, $minusDI) && BotManager::isAbove($ADX, $line_20)) {
            return BotManager::recordToRecommend('ADX', 'BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($plusDI, $minusDI) && BotManager::isAbove($ADX, $line_20)) {
            return BotManager::recordToRecommend('ADX', 'SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
    }

    /**
     * 
     * @param array $price
     * @param array $upperBB
     * @param array $lowerBB
     * @param type $ticker
     * @param type $date
     * @param type $avgVol
     * @param type $netBuy
     * @param type $close
     * @return type
     */
    public static function testBB(array $price, array $upperBB, array $lowerBB, $ticker, $date, $avgVol, $netBuy, $close) {
        if (BotManager::crossOver($price, $upperBB)) {
            return BotManager::recordToRecommend('BB', BotManager::compareTrend($price, $upperBB) ? 'FOLLOW UP' : 'FALLBACK', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($price, $lowerBB)) {
            return BotManager::recordToRecommend('BB', BotManager::compareTrend($price, $lowerBB) ? 'FOLLOW DOWN' : 'BOUNCE', $ticker, $date, $avgVol, $netBuy, $close);
        }
    }

    /**
     * 
     * @param array $CCI
     * @param array $line_minus100
     * @param array $line_100
     * @param array $line_minus200
     * @param array $line_200
     * @param type $ticker
     * @param type $date
     * @param type $avgVol
     * @param type $netBuy
     * @param type $close
     * @return type
     */
    public static function testCCI(array $CCI, array $line_minus100, array $line_100, array $line_minus200, array $line_200, $ticker, $date, $avgVol, $netBuy, $close) {
        if (BotManager::crossOver($CCI, $line_minus100)) {
            return BotManager::recordToRecommend('CCI', 'BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($CCI, $line_100)) {
            return BotManager::recordToRecommend('CCI', 'SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossOver($CCI, $line_minus200)) {
            return BotManager::recordToRecommend('CCI', 'STRONGBUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($CCI, $line_200)) {
            return BotManager::recordToRecommend('CCI', 'STRONGSELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
    }

    /**
     * 
     * @param array $MACD
     * @param array $MACDSignal
     * @param type $ticker
     * @param type $date
     * @param type $avgVol
     * @param type $netBuy
     * @param type $close
     * @return type
     */
    public static function testMACDSignal(array $MACD, array $MACDSignal, $ticker, $date, $avgVol, $netBuy, $close) {
        if (BotManager::crossOver($MACD, $MACDSignal) && $MACD <= 0 && $MACDSignal <= 0) {
            return BotManager::recordToRecommend('MACD', 'BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($MACD, $MACDSignal) && $MACD >= 0 && $MACDSignal >= 0) {
            return BotManager::recordToRecommend('MACD', 'SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
    }

    /**
     * 
     * @param array $RSI
     * @param array $line_30
     * @param array $line_70
     * @param type $ticker
     * @param type $date
     * @param type $avgVol
     * @param type $netBuy
     * @param type $close
     * @return type
     */
    public static function testRSI(array $RSI, array $line_30, array $line_70, $ticker, $date, $avgVol, $netBuy, $close) {
        if (BotManager::crossOver($RSI, $line_30)) {
            return BotManager::recordToRecommend('RSI', 'BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($RSI, $line_70)) {
            return BotManager::recordToRecommend('RSI', 'SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
    }

    /**
     * 
     * @param array $price
     * @param array $SAR
     * @param type $ticker
     * @param type $date
     * @param type $avgVol
     * @param type $netBuy
     * @param type $close
     * @return type
     */
    public static function testSAR(array $price, array $SAR, $ticker, $date, $avgVol, $netBuy, $close) {
        if (BotManager::crossOver($price, $SAR)) {
            return BotManager::recordToRecommend('SAR', 'BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($price, $SAR)) {
            return BotManager::recordToRecommend('SAR', 'SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
    }

    /**
     * 
     * @param array $price
     * @param array $EMA9
     * @param array $MA20
     * @param array $MA42
     * @param type $ticker
     * @param type $date
     * @param type $avgVol
     * @param type $netBuy
     * @param type $close
     * @return type
     */
    public static function testMA(array $price, array $EMA9, array $MA20, array $MA42, $ticker, $date, $avgVol, $netBuy, $close) {
        if (BotManager::crossOver($price, $EMA9)) {
            return BotManager::recordToRecommend('MA', 'EMA9 BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($price, $EMA9)) {
            return BotManager::recordToRecommend('MA', 'EMA9 SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossOver($price, $MA20)) {
            return BotManager::recordToRecommend('MA', 'MA20 BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($price, $MA20)) {
            return BotManager::recordToRecommend('MA', 'MA20 SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossOver($price, $MA42)) {
            return BotManager::recordToRecommend('MA', 'MA42 BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($price, $MA42)) {
            return BotManager::recordToRecommend('MA', 'MA42 SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossOver($EMA9, $MA20)) {
            return BotManager::recordToRecommend('MACross', '9/20 BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($EMA9, $MA20)) {
            return BotManager::recordToRecommend('MACross', '9/20 SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossOver($EMA9, $MA42)) {
            return BotManager::recordToRecommend('MACross', '9/42 BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($EMA9, $MA42)) {
            return BotManager::recordToRecommend('MACross', '9/42 SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossOver($MA20, $MA42)) {
            return BotManager::recordToRecommend('MACross', '20/42 BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($MA20, $MA42)) {
            return BotManager::recordToRecommend('MACross', '20/42 SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::testSupport($price, $EMA9)) {
            return BotManager::recordToRecommend('MASupport', 'EMA9 SUPPORT', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::testSupport($price, $MA20)) {
            return BotManager::recordToRecommend('MASupport', 'MA20 SUPPORT', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::testSupport($price, $MA42)) {
            return BotManager::recordToRecommend('MASupport', 'MA42 SUPPORT', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::testResistance($price, $EMA9)) {
            return BotManager::recordToRecommend('MAResistance', 'EMA9 RESISTANCE', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::testResistance($price, $MA20)) {
            return BotManager::recordToRecommend('MAResistance', 'MA20 RESISTANCE', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::testResistance($price, $MA42)) {
            return BotManager::recordToRecommend('MAResistance', 'MA42 RESISTANCE', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::BreakoutUp($price, $MA20)) {
            BotManager::recordToRecommend('MABreakout', 'MA20 BREAKOUT UP', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::BreakoutDown($price, $MA20)) {
            BotManager::recordToRecommend('MABreakout', 'MA20 BREAKOUT DOWN', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::BreakoutUp($price, $MA42)) {
            BotManager::recordToRecommend('MABreakout', 'MA42 BREAKOUT UP', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::BreakoutDown($price, $MA42)) {
            BotManager::recordToRecommend('MABreakout', 'MA42 BREAKOUT DOWN', $ticker, $date, $avgVol, $netBuy, $close);
        }
    }

    /**
     * 
     * @param array $price
     * @param array $MA42
     * @param type $ticker
     * @param type $date
     * @param type $avgVol
     * @param type $netBuy
     * @param type $close
     * @return type
     */
    public static function testMA_ver2(array $price, array $MA42, $ticker, $date, $avgVol, $netBuy, $close) {
        if (BotManager::crossOver($price, $MA42)) {
            return BotManager::recordToRecommend('MA', 'MA42 BUY', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::crossUnder($price, $MA42)) {
            return BotManager::recordToRecommend('MA', 'MA42 SELL', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::testSupport($price, $MA42)) {
            return BotManager::recordToRecommend('MASupport', 'MA42 SUPPORT', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::testResistance($price, $MA42)) {
            return BotManager::recordToRecommend('MAResistance', 'MA42 RESISTANCE', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::BreakoutUp($price, $MA42)) {
            BotManager::recordToRecommend('MABreakout', 'MA42 BREAKOUT UP', $ticker, $date, $avgVol, $netBuy, $close);
        }
        if (BotManager::BreakoutDown($price, $MA42)) {
            BotManager::recordToRecommend('MABreakout', 'MA42 BREAKOUT DOWN', $ticker, $date, $avgVol, $netBuy, $close);
        }
    }

    /**
     * Custom indicator from QuangHD
     * 
     * @param array $price
     * @param array $MA42
     * @param type $ticker
     * @param type $date
     * @param type $avgVol
     * @param type $netBuy
     * @param type $close
     */
    public static function testNewIndicator_One(array $price, array $MA42, array $line_0, $ticker, $date, $avgVol, $netBuy, $close) {
        $avg = [];
        $avg_end = 0;
        $avg_prev = 0;
        $count = count($price);

        if ($count > 1) {
            for ($i = 0; $i < $count; $i++) {
                $_change = ($price[$i] - $MA42[$i]) / $price[$i];

                $avg_prev += $i < $count ? $_change : 0; // end
                $avg_end += $i > 0 ? $_change : 0; // prev
            }

            $avg_prev = $avg_prev / ($count - 1);
            $avg_end = $avg_end / ($count - 1);

            array_push($avg, $avg_prev);
            array_push($avg, $avg_end);

            if (BotManager::crossOver($avg, $line_0)) {
                BotManager::recordToRecommend('IND1', 'IND1 SELL', $ticker, $date, $avgVol, $netBuy, $close);
            }

            if (BotManager::crossUnder($avg, $line_0)) {
                BotManager::recordToRecommend('IND1', 'IND1 BUY', $ticker, $date, $avgVol, $netBuy, $close);
            }
        }
    }

}
