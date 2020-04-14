<?php

namespace App\Managers;

use Carbon\Carbon;
use App\AccountTransactionLog;
use App\JobLog;
use App\ActionLog;

class LogManager {

    public static $QUEUE_STATUS = array(
        'Unknown' => 0,
        'Start' => 1,
        'End' => 2,
        'Error' => 3,
    );
    public static $ACTION_TYPE = array(
        'Unknown' => 0,
        'User' => 1,
        'Admin' => 2,
    );
    public static $ACTION_STATUS = array(
        'Unknown' => 0,
        'Ok' => 1,
        'Error' => 2,
    );

    /**
     * Get trade log
     * 
     * @param type $account_id
     * @return type
     */
    public static function getTradeLog($account_id) {
        $data = new AccountTransactionLog;
        return $data::where('account_id', $account_id)
                        ->orderBy('date', 'desc')
                        ->get();
    }

    /**
     * Add trade log
     * 
     * @param type $account_id
     * @param type $action
     * @param type $ticker
     * @param type $price
     * @param type $amount
     * @param type $money_before
     * @param type $money_after
     * @return AccountTransactionLog
     */
    public static function addTradeLog($account_id, $action, $ticker, $price, $amount, $money_before, $money_after) {
        $log = new AccountTransactionLog;
        $log->account_id = $account_id;
        $log->action = $action;
        $log->ticker = $ticker;
        $log->price = $price;
        $log->amount = $amount;
        $log->money_before = $money_before;
        $log->money_after = $money_after;
        $log->date = Carbon::now();
        $log->save();

        return $log;
    }

    /**
     * Get queue log
     * 
     * @return type
     */
    public static function getQueueLog($string = "") {
        $data = new JobLog;
        if (array_key_exists($string, LogManager::$QUEUE_STATUS)) {
            return $data::where('status', LogManager::$QUEUE_STATUS[$string])
                            ->orderBy('id', 'desc')
                            ->get();
        } else {
            return $data::where('action', 'LIKE', '%' . $string . '%')
                            ->orWhere('param', 'LIKE', '%' . $string . '%')
                            ->orWhere('comment', 'LIKE', '%' . $string . '%')
                            ->orderBy('id', 'desc')
                            ->get();
        }
    }

    /**
     * Save queue log
     * 
     * @param type $action
     * @param type $param
     * @param type $status
     * @param type $comment
     * @return JobLog
     */
    public static function addQueueLog($action, $param, $status, $comment) {
        $log = new JobLog;
        $log->date = Carbon::now();
        $log->action = $action;
        $log->param = $param;
        $log->status = $status;
        $log->comment = $comment;
        $log->save();

        return $log;
    }

    /**
     * Get action log
     * 
     * @return type
     */
    public static function getActionLog($string = "", $limit = 100) {
        $data = new ActionLog;
        if (array_key_exists($string, LogManager::$ACTION_TYPE)) {
            return $data::where('type', LogManager::$ACTION_TYPE[$string])
                            ->orderBy('id', 'desc')
                            ->take($limit)
                            ->get();
        } else if (array_key_exists($string, LogManager::$ACTION_STATUS)) {
            return $data::where('status', LogManager::$ACTION_STATUS[$string])
                            ->orderBy('id', 'desc')
                            ->take($limit)
                            ->get();
        } else {
            return $data::where('action', 'LIKE', '%' . $string . '%')
                            ->orWhere('param', 'LIKE', '%' . $string . '%')
                            ->orWhere('comment', 'LIKE', '%' . $string . '%')
                            ->orderBy('id', 'desc')
                            ->take($limit)
                            ->get();
        }
    }

    /**
     * Save action log
     * 
     * @param type $user_id
     * @param type $action
     * @param type $param
     * @param type $type
     * @param type $status
     * @param type $comment
     * @return JobLog
     */
    public static function addActionLog($user_id, $action, $param, $type, $status, $comment = '') {
        $log = new ActionLog;
        $log->user_id = $user_id;
        $log->date = Carbon::now();
        $log->action = $action;
        $log->param = $param;
        $log->type = $type;
        $log->status = $status;
        $log->comment = $comment;
        $log->save();

        return $log;
    }

}
