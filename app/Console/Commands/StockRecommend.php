<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Ticker;
use App\TickerBot;
use App\TickerData;
use App\TickerQuote;
use App\TickerRecommend;
use App\Managers\BotManager;
use App\Managers\CacheManager;
use App\Managers\CacheKey;
use App\Managers\LogManager;

class StockRecommend extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CalculateRecommend {--truncate=0} {--date=} {--clear=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Filter recommend stocks from calculated data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        try {
            Log::debug("==== START StockRecommend ====");
            $truncate = $this->option('truncate');
            $calculateDate = $this->option('date');
            $clear = $this->option('clear');
            LogManager::addQueueLog('CalculateRecommend', 'calculateDate: ' . $calculateDate . ' - clear: ' . $clear, LogManager::$QUEUE_STATUS['Start'], '');
            if ($truncate) {
                TickerRecommend::truncate();
            }

            $tickerArray = Ticker::lists('Ticker')->all();
            $line_minus200 = [-200, -200];
            $line_minus100 = [-100, -100];
            $line_0 = [0, 0];
            $line_20 = [20, 20];
            $line_30 = [30, 30];
            $line_70 = [70, 70];
            $line_80 = [80, 80];
            $line_100 = [100, 100];
            $line_200 = [200, 200];

            $date = TickerBot::checkDate($calculateDate) ? $calculateDate : TickerBot::getLatestDate();
            Log::debug(' Date: ' . $date);

            if (!$clear && TickerRecommend::where('date', $date)->first() !== null) {
                LogManager::addQueueLog('CalculateRecommend', 'calculateDate: ' . $calculateDate . ' - clear: ' . $clear, LogManager::$QUEUE_STATUS['End'], 'Already has data');
                Log::debug("==== END StockRecommend - Had data ====");
                return;
            }

            TickerRecommend::where('date', $date)->delete(); // Delete same day data

            foreach ($tickerArray as $ticker) {
                Log::debug(' Ticker: ' . $ticker);

                $avgVol = TickerBot::select('avg_volume_20')
                        ->where('ticker', $ticker)
                        ->where('date', $date)
                        ->first();

                if ($avgVol !== null) {
                    $avgVol = $avgVol->avg_volume_20;
                    $closePrice = TickerData::ChooseEndAndPrev($ticker, 'close', $date, 43); // 2 months data
                    $close = end($closePrice); // close price of that day
                    $MA42 = TickerBot::ChooseEndAndPrev($ticker, 'MA42', $date, 43); // 2 months data
                    $MACD = TickerBot::ChooseEndAndPrev($ticker, 'MACD', $date);
                    $MACDSignal = TickerBot::ChooseEndAndPrev($ticker, 'MACDSignal', $date);
                    $CCI = TickerBot::ChooseEndAndPrev($ticker, 'CCI', $date);
                    $upperBB = TickerBot::ChooseEndAndPrev($ticker, 'upperBB', $date);
                    $lowerBB = TickerBot::ChooseEndAndPrev($ticker, 'lowerBB', $date);
                    $netBuy = TickerQuote::netBuy($ticker, $date);
                    //Log::debug(' Avg Vol: ' . $avgVol);
                    //$EMA9 = TickerBot::ChooseEndAndPrev($ticker, 'EMA9', $date);
                    //$MA20 = TickerBot::ChooseEndAndPrev($ticker, 'MA20', $date);
                    //$RSI = TickerBot::ChooseEndAndPrev($ticker, 'RSI', $date);
                    //$SAR = TickerBot::ChooseEndAndPrev($ticker, 'SAR', $date);
                    //$ADX = TickerBot::ChooseEndAndPrev($ticker, 'ADX', $date);
                    //$plusDI = TickerBot::ChooseEndAndPrev($ticker, 'plusDI', $date);
                    //$minusDI = TickerBot::ChooseEndAndPrev($ticker, 'minusDI', $date);

                    BotManager::testMACDSignal($MACD, $MACDSignal, $ticker, $date, $avgVol, $netBuy, $close);
                    BotManager::testBB($closePrice, $upperBB, $lowerBB, $ticker, $date, $avgVol, $netBuy, $close);
                    BotManager::testCCI($CCI, $line_minus100, $line_100, $line_minus200, $line_200, $ticker, $date, $avgVol, $netBuy, $close);
                    BotManager::testMA_ver2($closePrice, $MA42, $ticker, $date, $avgVol, $netBuy, $close);
                    BotManager::testNewIndicator_One($closePrice, $MA42, $line_0, $ticker, $date, $avgVol, $netBuy, $close);
                    //BotManager::testMA($closePrice, $EMA9, $MA20, $MA42, $ticker, $date, $avgVol, $netBuy, $close);
                    //BotManager::testRSI($RSI, $line_30, $line_70, $ticker, $date, $avgVol, $netBuy, $close);
                    //BotManager::testSAR($closePrice, $SAR, $ticker, $date, $avgVol, $netBuy, $close);
                    //BotManager::testADX($ADX, $line_20, $plusDI, $minusDI, $ticker, $date, $avgVol, $netBuy, $close);
                }
            }

            // Remove cached data
            CacheManager::Remove(CacheKey::TickerRecommend());
            LogManager::addQueueLog('CalculateRecommend', 'calculateDate: ' . $calculateDate . ' - clear: ' . $clear, LogManager::$QUEUE_STATUS['End'], '');
            Log::debug("==== END StockRecommend ====");
        } catch (\Exception $e) {
            Log::debug("==== ERROR StockRecommend ====" . $e->getMessage());
            LogManager::addQueueLog('CalculateRecommend', '', LogManager::$QUEUE_STATUS['Error'], $e->getMessage());
        }
    }

}
