<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\TickerChart;

//use JWTAuth;
//use Tymon\JWTAuth\Exceptions\JWTException;

class TickerChartTemplateController extends Controller {

    public function __construct() {
        $this->middleware('cors');
        //$this->middleware('jwt.auth');
        //$this->middleware('jwt.refresh');
        //$this->middleware('permission:ticker-chart-api');
    }

    /**
     * Get chart template list
     * 
     * @return type
     */
    public function getCharts() {
        //$client = Input::has('client') ? Input::get('client') : ""; // Client info. Original = tradingview.com
        $user = Input::has('user') ? Input::get('user') : ""; // User unfo, Original = public_user
        $chart = Input::has('chart') ? Input::get('chart') : 0; // Chart ID. If id = 0 then load all chart

        try {
            if ($chart) {
                $temp = TickerChart::getChartById($chart, $user)->toArray();
                if (count($temp)) {
                    $temp[0]['timestamp'] = strtotime($temp[0]['timestamp']);
                    $result['data'] = $temp[0];
                }
            } else {
                $result['data'] = TickerChart::getChartList($user)->toArray();
                for ($i = 0; $i < count($result['data']); $i++) {
                    $result['data'][$i]['timestamp'] = strtotime($result['data'][$i]['timestamp']);
                }
            }
            $result['status'] = 'ok';
        } catch (\Exception $e) {
            $result['status'] = 'error';
            $result['message'] = 'Server error. Exception: ' . $e->getMessage();
        }

        return Response::json(json_encode($result));
    }

    /**
     * Upload chart template
     * 
     * @return type
     */
    public function postCharts() {
        //$client = Input::has('client') ? Input::get('client') : ""; // Client info. Original = tradingview.com
        $user = Input::has('user') ? Input::get('user') : ""; // User unfo, Original = public_user
        $chart = Input::has('chart') ? Input::get('chart') : 0; // Chart id, used for edit chart

        $name = Input::has('name') ? Input::get('name') : "NO NAME"; // Template name
        $content = Input::has('content') ? Input::get('content') : ""; // Content
        $symbol = Input::has('symbol') ? Input::get('symbol') : "SYMBOL"; // Symbol
        $resolution = Input::has('resolution') ? Input::get('resolution') : "D"; // Resolution

        if (strlen($content)) {
            try {
                $tickerChart = TickerChart::getChart($chart, $user);
                if ($tickerChart === null) {
                    $tickerChart = new TickerChart;
                }

                $tickerChart->user_id = $user;
                $tickerChart->name = $name;
                $tickerChart->content = $content;
                $tickerChart->symbol = $symbol;
                $tickerChart->resolution = $resolution;
                $tickerChart->save();

                $result['status'] = 'ok';
                $result['message'] = 'Upload OK';
                $result['id'] = $tickerChart->id;
            } catch (\Exception $e) {
                $result['status'] = 'error';
                $result['message'] = 'Server error. Exception: ' . $e->getMessage();
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = 'No content data';
        }
        return Response::json(json_encode($result));
    }

    /**
     * Delete chart template
     * 
     * @return type
     */
    public function deleteCharts() {
        //$client = Input::has('client') ? Input::get('client') : ""; // Client info. Original = tradingview.com
        $user = Input::has('user') ? Input::get('user') : ""; // User unfo, Original = public_user

        $chart = Input::has('chart') ? Input::get('chart') : 0; // Chart ID

        if ($chart !== 0 && TickerChart::where('id', $chart)->where('user_id', $user)->delete()) {
            $result['status'] = 'ok';
            return Response::json(json_encode($result));
        } else {
            return Response::json([
                        'error' => true,
                        'message' => 'Can not delete chart',
                        'code' => 403
                            ], 403);
        }
    }

    /**
     * Load chart template option
     * 
     * @return type
     */
    public function optionsCharts() {
        //$client = Input::has('client') ? Input::get('client') : ""; // Client info. Original = tradingview.com
        //$user = Input::has('user') ? Input::get('user') : ""; // User unfo, Original = public_user
        $result['status'] = 'ok';
        return Response::json(json_encode($result));
    }

}
