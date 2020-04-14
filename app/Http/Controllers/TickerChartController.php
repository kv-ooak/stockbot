<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Ticker;
use App\TickerData;
use App\Managers\CacheManager;
use App\Managers\CacheKey;
use App\Managers\ConfigManager;
use App\Managers\LogManager;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class TickerChartController extends Controller {

    const TimeStampMax = 2147483647;

    public function __construct(JWTAuth $auth) {
        $this->middleware('cors');
        $this->middleware('jwt.auth');
        $this->middleware('jwt.refresh', ['except' => ['getSearch', 'getHistory']]);
        $this->middleware('permission:ticker-chart-api');

        // User info
        try {
            $this->user = $auth::parseToken()->toUser();
            $this->user_id = $this->user['id'];
        } catch (JWTException $e) {
            $this->user_id = 0;
        }
    }

    /**
     * Init chart view setting
     * 
     * @return type
     */
    public function init() {
        try {
            $result = [
                'client_id' => 'lumine',
                'user_id' => $this->user_id,
            ];

            LogManager::addActionLog($this->user_id, 'Chart', 'init', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (JWTException $e) {
            LogManager::addActionLog($this->user_id, 'Chart', 'init', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get config for chart view
     * 
     * @return type
     */
    public function getConfig() {
        try {
            $result = array(
                'supports_search' => true,
                'supports_group_request' => false,
                'supported_resolutions' => ["D", "2D", "3D", "W", "3W", "M", "6M"],
                'supports_marks' => false,
                'supports_time' => true,
            );

            LogManager::addActionLog($this->user_id, 'Chart', 'getConfig', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json(json_encode($result));
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Chart', 'getConfig', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get server time
     * 
     * @return type
     */
    public function getTime() {
        try {
            $result = strtotime(Carbon::now());

            LogManager::addActionLog($this->user_id, 'Chart', 'getTime', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json(json_encode($result));
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Chart', 'getTime', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get symbol info
     * 
     * @return type
     */
    public function getSymbols() {
        try {
            $symbol = Input::has('symbol') ? Input::get('symbol') : ""; //string. Symbol name or ticker.

            $result = [];
            $result['name'] = $symbol;
            $result['ticker'] = $symbol;
            $result['description'] = "";
            $result['type'] = "stock";
            $result['session'] = "0930-1630";
            $result['exchange-traded'] = "";
            $result['exchange-listed'] = "";
            $result['timezone'] = "Asia/Bangkok";
            $result['pricescale'] = 10;
            $result['pointvalue'] = 1;
            $result['minmov'] = 1;
            $result['fractional'] = false;
            $result['minmove2'] = 0;
            $result['has_intraday'] = false;
            $result['supported_resolutions'] = ["D", "2D", "3D", "W", "3W", "M", "6M"];
            $result['has_no_volume'] = false;

            LogManager::addActionLog($this->user_id, 'Chart', 'getSymbols', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json(json_encode($result));
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Chart', 'getSymbols', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Return search symbol result
     * 
     * @return type
     */
    public function getSearch() {
        try {
            $query = Input::has('query') ? Input::get('query') : ""; //string. Text typed by user in Symbol Search edit box
            $type = Input::has('type') ? Input::get('type') : ""; //string. One of the types supported by your back-end
            $exchange = Input::has('exchange') ? Input::get('exchange') : ""; //string. One of the exchanges supported by your back-end
            $limit = Input::has('limit') ? Input::get('limit') : 1; //integer. Maximal items count in response
            // TODO: Add exchange and type logic
            $tickerList = Ticker::searchTicker($query, $limit);
            $result = [];
            foreach ($tickerList as $item) {
                array_push($result, [
                    'symbol' => $item->ticker,
                    'full_name' => $item->ticker,
                    'description' => 'No description found',
                    'exchange' => $item->exchange,
                    'ticker' => $item->ticker,
                    'type' => 'stock'
                ]);
            }

            LogManager::addActionLog($this->user_id, 'Chart', 'getSearch', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $query);
            return Response::json(json_encode($result));
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Chart', 'getSearch', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get symbol history data
     * 
     * @return type
     */
    public function getHistory() {
        try {
            $symbol = Input::has('symbol') ? Input::get('symbol') : ""; //symbol name or ticker.
            $from = date("Y-m-d", Input::has('from') ? Input::get('from') : 0); //unix timestamp (UTC) or leftmost required bar
            $to = date("Y-m-d", Input::has('to') ? Input::get('to') : TickerChartController::TimeStampMax); //unix timestamp (UTC) or rightmost required bar
            $resolution = Input::has('resolution') ? Input::get('resolution') : ""; //string
            //TODO: Add resolution logic

            $result = [];

            if (ConfigManager::CacheEnable() && Cache::has(CacheKey::TickerChartData($symbol, $from, $to))) {
                $result = Cache::get(CacheKey::TickerChartData($symbol, $from, $to));
            } else {
                $date = TickerData::ChooseRange($symbol, 'date', $from, $to);

                if (is_object($date)) {
                    $result['s'] = 'no_data';
                    //$result['nextTime'] = 1892798371; // Optional
                } else {
                    $openPrice = TickerData::ChooseRange($symbol, 'open', $from, $to);
                    $highPrice = TickerData::ChooseRange($symbol, 'high', $from, $to);
                    $lowPrice = TickerData::ChooseRange($symbol, 'low', $from, $to);
                    $closePrice = TickerData::ChooseRange($symbol, 'close', $from, $to);
                    $volume = TickerData::ChooseRange($symbol, 'volume', $from, $to);

                    $timestamp = [];
                    foreach ($date as $tmpDate) {
                        array_push($timestamp, strtotime($tmpDate));
                    }

                    $result['s'] = 'ok';
                    $result['t'] = $timestamp;
                    $result['c'] = $closePrice;
                    $result['o'] = $openPrice;
                    $result['h'] = $highPrice;
                    $result['l'] = $lowPrice;
                    $result['v'] = $volume;

                    // Add cache for faster view
                    CacheManager::AddNew(CacheKey::TickerChartData($symbol, $from, $to), $result, CacheManager::time5min);
                }
            }

            LogManager::addActionLog($this->user_id, 'Chart', 'getHistory', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $symbol);
            return Response::json(json_encode($result));
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Chart', 'getHistory', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

}
