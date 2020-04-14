<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Ticker;
use App\TickerData;
use App\TickerBot;
use App\TickerRecommend;
use App\TickerQuote;
use App\Managers\LogManager;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class TickerController extends Controller {

    public function __construct(Request $request, JWTAuth $auth) {
        $this->middleware('cors');
        $this->middleware('jwt.auth');
        $this->middleware('jwt.refresh', ['except' => ['getData', 'getBot', 'getRecommendDetail', 'getRecommendDateList', 'getQuote']]);
        $this->middleware('permission:ticker-api');

        //Datatable parameter
        $this->draw = $request->input('draw');
        //paging parameter
        $this->start = $request->input('start');
        $this->length = $request->input('length');
        //sorting parameter
        $this->sortColumn = $request->input('columns')[$request->input('order')[0]['column']]['data'];
        $this->sortColumnDir = $request->input('order')[0]['dir'];
        //filter parameter
        $this->searchValue = $request->input('search')['value'];

        $this->pageSize = $this->length != null ? $this->length : 0;
        $this->skip = $this->start != null ? $this->start : 0;

        // User info
        try {
            $this->user = $auth::parseToken()->toUser();
            $this->user_id = $this->user['id'];
        } catch (JWTException $e) {
            $this->user_id = 0;
        }
    }

    /**
     * Get ticker info
     * 
     * @return type
     */
    public function getTicker() {
        try {
            $ticker = Input::has('ticker') ? Input::get('ticker') : '';
            $date = Input::has('date') ? Input::get('date') : null;
            if ($date !== null) {
                $tickerData = TickerData::where('ticker', $ticker)
                        ->where('date', $date)
                        ->limit(1)
                        ->get();
            } else {
                $tickerData = TickerData::where('ticker', $ticker)
                        ->orderBy('date', 'desc')
                        ->limit(1)
                        ->get();
            }

            $result['data'] = $tickerData;

            LogManager::addActionLog($this->user_id, 'Ticker', 'getTicker', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $ticker);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Ticker', 'getTicker', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * 
     * @return type
     */
    public function getList() {
        try {
            //DB query
            $_data = Ticker::searchTicker($this->searchValue)->toArray();
            //Sort
            if ($this->sortColumn != null && strlen($this->sortColumn) > 0 && $this->sortColumnDir != null && strlen($this->sortColumnDir)) {
                usort($_data, function($a, $b) {
                    return $this->sortColumnDir === 'asc' ? $a[$this->sortColumn] > $b[$this->sortColumn] : $a[$this->sortColumn] < $b[$this->sortColumn];
                });
            }
            $recordsTotal = count($_data);
            $data = array_slice($_data, $this->skip, $this->pageSize);
            //Result
            $result = Array(
                'draw' => $this->draw,
                'recordsFiltered' => $recordsTotal,
                'recordsTotal' => $recordsTotal,
                'data' => $data,
            );

            LogManager::addActionLog($this->user_id, 'Ticker', 'getList', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $this->searchValue);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Ticker', 'getList', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get raw data of ticker
     * 
     * @param type $ticker
     * @return type
     */
    public function getData($ticker) {
        try {
            //DB query
            $_data = TickerData::getDataByTicker($ticker)->toArray();
            //Sort
            if ($this->sortColumn != null && strlen($this->sortColumn) > 0 && $this->sortColumnDir != null && strlen($this->sortColumnDir)) {
                usort($_data, function($a, $b) {
                    return $this->sortColumnDir === 'asc' ? $a[$this->sortColumn] > $b[$this->sortColumn] : $a[$this->sortColumn] < $b[$this->sortColumn];
                });
            }
            $recordsTotal = count($_data);
            $data = array_slice($_data, $this->skip, $this->pageSize);
            //Result
            $result = Array(
                'draw' => $this->draw,
                'recordsFiltered' => $recordsTotal,
                'recordsTotal' => $recordsTotal,
                'data' => $data,
            );

            LogManager::addActionLog($this->user_id, 'Ticker', 'getData', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $ticker);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Ticker', 'getData', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get calculated data of ticker
     * 
     * @param type $ticker
     * @return type
     */
    public function getBot($ticker) {
        try {
            //DB query
            $_data = TickerBot::getDataByTicker($ticker)->toArray();
            //Sort
            if ($this->sortColumn != null && strlen($this->sortColumn) > 0 && $this->sortColumnDir != null && strlen($this->sortColumnDir)) {
                usort($_data, function($a, $b) {
                    return $this->sortColumnDir === 'asc' ? $a[$this->sortColumn] > $b[$this->sortColumn] : $a[$this->sortColumn] < $b[$this->sortColumn];
                });
            }
            $recordsTotal = count($_data);
            $data = array_slice($_data, $this->skip, $this->pageSize);
            //Result
            $result = Array(
                'draw' => $this->draw,
                'recordsFiltered' => $recordsTotal,
                'recordsTotal' => $recordsTotal,
                'data' => $data,
            );

            LogManager::addActionLog($this->user_id, 'Ticker', 'getBot', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $ticker);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Ticker', 'getBot', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get recommend ticker list
     * 
     * @return type
     */
    public function getRecommend() {
        try {
            $date = Input::has('date') ? Input::get('date')['date'] : TickerRecommend::getLatestDates();
            //DB query
            $_temp_data = TickerRecommend::searchTickerWithDate($this->searchValue, $date)
                    ->groupBy('ticker')
                    ->map(function($item) {
                return $item->all();
            });

            $_data = [];
            foreach ($_temp_data as $key => $value) {
                $t = [];
                $t['date'] = $date;
                $t['ticker'] = $key;
                $t['avg_volume_20'] = $value[0]['avg_volume_20'];
                $t['price'] = $value[0]['price'];
                $t['net_buy'] = $value[0]['net_buy'];
                $t['net_buy_value'] = $value[0]['net_buy_value'];
                $t['indicator'] = "";
                $t['signal'] = "";
                $t['strength'] = "";
                foreach ($value as $k => $v) {
                    $t['indicator'] .= $v['indicator'] . "<br/>";
                    $t['signal'] .= $v['signal'] . "<br/>";
                    if ($v['strength'] == 'POS') {
                        $t['strength'] .= '<font size = "5" color = "green"><b>' . $v['arrow'] . '</b></font>';
                    } else if ($v['strength'] == 'NEG') {
                        $t['strength'] .= '<font size = "5" color = "red"><b>' . $v['arrow'] . '</b></font>';
                    } else if ($v['strength'] == 'NEU') {
                        $t['strength'] .= '<font size = "5" color = "CCCC00"><b>' . $v['arrow'] . '</b></font>';
                    }
                }
                array_push($_data, $t);
            }

            //Sort
            if ($this->sortColumn != null && strlen($this->sortColumn) > 0 && $this->sortColumnDir != null && strlen($this->sortColumnDir)) {
                usort($_data, function($a, $b) {
                    return $this->sortColumnDir === 'asc' ? $a[$this->sortColumn] > $b[$this->sortColumn] : $a[$this->sortColumn] < $b[$this->sortColumn];
                });
            }
            $recordsTotal = count($_data);
            $data = array_slice($_data, $this->skip, $this->pageSize);
            //Result
            $result = Array(
                'draw' => $this->draw,
                'recordsFiltered' => $recordsTotal,
                'recordsTotal' => $recordsTotal,
                'data' => $data,
            );

            LogManager::addActionLog($this->user_id, 'Ticker', 'getRecommend', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $this->searchValue);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Ticker', 'getRecommend', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Return date list in recommend data
     * 
     * @return type
     */
    public function getRecommendDateList() {
        try {
            $result['data'] = TickerRecommend::getDateList();

            LogManager::addActionLog($this->user_id, 'Ticker', 'getRecommendDateList', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Ticker', 'getRecommendDateList', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get recommend data of ticker
     * 
     * @param type $ticker
     * @return type
     */
    public function getRecommendDetail($ticker) {
        try {
            //DB query
            $temp_data = TickerRecommend::searchTicker($this->searchValue, $ticker)
                    ->sortByDesc('date')
                    ->groupBy('date')
                    ->map(function($item) {
                return $item->all();
            });

            $_data = [];
            foreach ($temp_data as $key => $value) {
                $temp['date'] = $key;
                $temp['avg_volume_20'] = $value[0]['avg_volume_20'];
                $temp['price'] = $value[0]['price'];
                $temp['net_buy'] = $value[0]['net_buy'];
                $temp['net_buy_value'] = $value[0]['net_buy_value'];
                $temp['indicator'] = "";
                $temp['signal'] = "";
                $temp['strength'] = "";
                foreach ($value as $k => $v) {
                    $temp['indicator'] .= $v['indicator'] . "<br/>";
                    $temp['signal'] .= $v['signal'] . "<br/>";
                    if ($v['strength'] == 'POS') {
                        $temp['strength'] .= '<font size = "5" color = "green"><b>' . $v['arrow'] . '</b></font>';
                    } else if ($v['strength'] == 'NEG') {
                        $temp['strength'] .= '<font size = "5" color = "red"><b>' . $v['arrow'] . '</b></font>';
                    } else if ($v['strength'] == 'NEU') {
                        $temp['strength'] .= '<font size = "5" color = "CCCC00"><b>' . $v['arrow'] . '</b></font>';
                    }
                }
                array_push($_data, $temp);
            }

            //Sort
            if ($this->sortColumn != null && strlen($this->sortColumn) > 0 && $this->sortColumnDir != null && strlen($this->sortColumnDir)) {
                usort($_data, function($a, $b) {
                    return $this->sortColumnDir === 'asc' ? $a[$this->sortColumn] > $b[$this->sortColumn] : $a[$this->sortColumn] < $b[$this->sortColumn];
                });
            }
            $recordsTotal = count($_data);
            $data = array_slice($_data, $this->skip, $this->pageSize);
            //Result
            $result = Array(
                'draw' => $this->draw,
                'recordsFiltered' => $recordsTotal,
                'recordsTotal' => $recordsTotal,
                'data' => $data,
            );

            LogManager::addActionLog($this->user_id, 'Ticker', 'getRecommendDetail', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $ticker);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Ticker', 'getRecommendDetail', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get quote data
     * 
     * @return type
     */
    public function getQuote() {
        try {
            $date = Input::has('date') ? Input::get('date')['date'] : TickerQuote::getLatestDates();
            $ticker = Input::has('ticker') ? Input::get('ticker')['ticker'] : '';

            //DB query
            $_data = TickerQuote::getDataByTickerAndDate($ticker, $date, $this->searchValue)->toArray();

            //Sort
            if ($this->sortColumn != null && strlen($this->sortColumn) > 0 && $this->sortColumnDir != null && strlen($this->sortColumnDir)) {
                usort($_data, function($a, $b) {
                    return $this->sortColumnDir === 'asc' ? $a[$this->sortColumn] > $b[$this->sortColumn] : $a[$this->sortColumn] < $b[$this->sortColumn];
                });
            }
            $recordsTotal = count($_data);
            $data = array_slice($_data, $this->skip, $this->pageSize);
            //Result
            $result = Array(
                'draw' => $this->draw,
                'recordsFiltered' => $recordsTotal,
                'recordsTotal' => $recordsTotal,
                'data' => $data,
            );

            LogManager::addActionLog($this->user_id, 'Ticker', 'getQuote', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $this->searchValue);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Ticker', 'getQuote', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Return list data in quote data
     * 
     * @return type
     */
    public function getQuoteList() {
        try {
            $result['data'] = [];
            $result['data']['date'] = TickerQuote::getDateList();
            $result['data']['ticker'] = TickerQuote::getTickerList();

            LogManager::addActionLog($this->user_id, 'Ticker', 'getQuoteDateList', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Ticker', 'getQuoteDateList', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

}
