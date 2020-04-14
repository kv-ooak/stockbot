<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Ticker;
use App\TickerBot;
use App\TickerData;
use App\Managers\ConfigManager;
use App\Managers\LogManager;

class StockBot extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CalculateBot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate stocks data from raw data';

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
            Log::debug("==== START StockBot ====");
            LogManager::addQueueLog('CalculateBot', '', LogManager::$QUEUE_STATUS['Start'], '');

            // Clear data before fill
            TickerBot::truncate();

            $tickerArray = Ticker::lists('Ticker')->all();
            $tickerBotData = [];
            $dataCount = 0;

            foreach ($tickerArray as $ticker) {
                Log::debug(' Ticker: ' . $ticker);
                $date = TickerData::Choose($ticker, 'date');

                if (!is_object($date)) {
                    //$openPrice = TickerData::Choose($ticker, 'open'); // unused
                    $highPrice = TickerData::Choose($ticker, 'high');
                    $lowPrice = TickerData::Choose($ticker, 'low');
                    $closePrice = TickerData::Choose($ticker, 'close');
                    $volume = TickerData::Choose($ticker, 'volume');
                    $dateCount = count($date);
                    //$SAR = trader_sar($highPrice, $lowPrice, 0.02, 0.2);
                    switch ($dateCount):
                        //case ($dateCount >= 9):
                        //$EMA9 = trader_ema($closePrice, 9);
                        //case ($dateCount >= 14):
                        //$RSI = trader_rsi($closePrice, 14);
                        //$ADX = trader_adx($highPrice, $lowPrice, $closePrice, 14);
                        //$plusDI = trader_plus_di($highPrice, $lowPrice, $closePrice, 14);
                        //$minusDI = trader_minus_di($highPrice, $lowPrice, $closePrice, 14);

                        case ($dateCount >= 20):
                            //$MA20 = trader_sma($closePrice, 20);
                            $CCI = trader_cci($highPrice, $lowPrice, $closePrice, 20);
                            $BB = trader_bbands($closePrice, 20, 2, 2, TRADER_MA_TYPE_SMA);
                            $upperBB = $BB[0];
                            $lowerBB = $BB[2];
                            $divideBy_20 = function ($number) {
                                return ($number / 20);
                            };
                            for ($i = 19; $i < $dateCount; $i++) {
                                $aggregateVol_20[$i] = Null;
                                foreach (range(0, 19) as $j) {
                                    $aggregateVol_20[$i] += $volume[$i - $j];
                                }
                            }
                            $avgVol = array_map($divideBy_20, $aggregateVol_20);

                        case ($dateCount >= 26):
                            $MACD = trader_macd($closePrice, 12, 26, 9);
                            $MACDLine = $MACD[0];
                            $MACDSignal = $MACD[1];

                        case ($dateCount >= 42):
                            $MA42 = trader_sma($closePrice, 42);
                            break;
                    endswitch;

                    for ($i = 0; $i < $dateCount; $i++) {
                        $bot = array(
                            'ticker' => $ticker,
                            'date' => $date[$i],
                            'avg_volume_20' => isset($avgVol[$i]) ? $avgVol[$i] : 0,
                            'EMA9' => isset($EMA9[$i]) ? $EMA9[$i] : 0,
                            'MA20' => isset($MA20[$i]) ? $MA20[$i] : 0,
                            'MA42' => isset($MA42[$i]) ? $MA42[$i] : 0,
                            'MACD' => isset($MACDLine[$i]) ? $MACDLine[$i] : 0,
                            'MACDSignal' => isset($MACDSignal[$i]) ? $MACDSignal[$i] : 0,
                            'RSI' => isset($RSI[$i]) ? $RSI[$i] : 0,
                            'SAR' => isset($SAR[$i]) ? $SAR[$i] : 0,
                            'CCI' => isset($CCI[$i]) ? $CCI[$i] : 0,
                            'ADX' => isset($ADX[$i]) ? $ADX[$i] : 0,
                            'plusDI' => isset($plusDI[$i]) ? $plusDI[$i] : 0,
                            'minusDI' => isset($minusDI[$i]) ? $minusDI[$i] : 0,
                            'upperBB' => isset($upperBB[$i]) ? $upperBB[$i] : 0,
                            'lowerBB' => isset($lowerBB[$i]) ? $lowerBB[$i] : 0,
                        );
                        array_push($tickerBotData, $bot);
                        $dataCount++;
                    }
                }

                if ($dataCount >= 10000) {
                    Log::debug("==== Bulk Insert Data " . $dataCount . " ====");
                    $insertChunks = array_chunk($tickerBotData, 1000);
                    foreach ($insertChunks as $chunk) {
                        TickerBot::insert($chunk);
                    }
                    $tickerBotData = [];
                    $dataCount = 0;
                }
            }

            if (count($tickerBotData) > 0) {
                Log::debug("==== Bulk Insert Data " . count($tickerBotData) . " ====");
                $insertChunks = array_chunk($tickerBotData, 1000);
                foreach ($insertChunks as $chunk) {
                    TickerBot::insert($chunk);
                }
            }
            LogManager::addQueueLog('CalculateBot', '', LogManager::$QUEUE_STATUS['End'], '');
            Log::debug("==== END StockBot ====");
        } catch (\Exception $e) {
            Log::debug("==== ERROR StockBot ====" . $e->getMessage());
            LogManager::addQueueLog('CalculateBot', '', LogManager::$QUEUE_STATUS['Error'], $e->getMessage());
        }
    }

}
