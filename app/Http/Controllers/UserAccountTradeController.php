<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Ticker;
use App\TickerData;
use App\UserAccount;
use App\AccountBuyItem;
use App\AccountSellBuy;
use App\AccountSellItem;
use App\Managers\TradeManager;
use App\Managers\LogManager;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserAccountTradeController extends Controller {

    public function __construct(JWTAuth $auth) {
        $this->middleware('cors');
        $this->middleware('jwt.auth');
        $this->middleware('jwt.refresh');
        $this->middleware('permission:user-account-trade-api');

        // User and account info
        try {
            $this->user = $auth::parseToken()->toUser();
            $this->user_id = $this->user['id'];
            $account_id = Input::has('account_id') ? Input::get('account_id') : (Input::has('account') ? Input::get('account')['id'] : 0);
            $this->account = $account_id > 0 ? UserAccount::getAccount($account_id, $this->user_id) : null;
        } catch (JWTException $e) {
            $this->user_id = 0;
            $this->account = null;
        }
    }

    /**
     * Get account remain item and other tons of info
     * 
     * @return type
     */
    public function getAccountSummary() {
        try {
            // Data validation
            if ($this->account === null) {
                LogManager::addActionLog($this->user_id, 'Trade', 'getAccountSummary', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], 'Invalid account');
                return Response::json([
                            'error' => true,
                            'message' => 'Can not get data',
                            'code' => 500
                                ], 500);
            }

            // Main process
            $list = AccountBuyItem::getItemAmountGroupByTicker($this->account->id);
            for ($i = 0; $i < count($list); $i++) {
                $tickerData = TickerData::where('ticker', $list[$i]->ticker)
                        ->orderBy('date', 'desc')
                        ->limit(1)
                        ->get();
                $list[$i]->tickerData = $tickerData;
            }

            $_history = AccountSellBuy::getLog($this->account->id)
                    ->groupBy('sell_id')
                    ->map(function($item) {
                return $item->all();
            });

            $history = [];
            foreach ($_history as $key => $value) {
                $h = [];
                $h['id'] = $key;
                $h['sell_date'] = $value[0]['sell_date'];
                $h['ticker'] = $value[0]['ticker'];
                $h['sell_price'] = $value[0]['sell_price'];
                $h['amount'] = 0;
                $h['value'] = 0;
                foreach ($value as $v) {
                    $h['amount'] += $v['amount'];
                    $h['value'] += $v['amount'] * $v['buy_price'];
                }

                array_push($history, $h);
            }

            $result['data'] = [];
            $result['data']['account_item'] = $list;
            $result['data']['account_transaction'] = LogManager::getTradeLog($this->account->id);
            $result['data']['account_history'] = $history;
            $result['status'] = 'ok';

            LogManager::addActionLog($this->user_id, 'Trade', 'getAccountSummary', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $this->account->id);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Trade', 'getAccountSummary', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get data for buy item form
     * 
     * @return type
     */
    public function buyItemForm() {
        try {
            // Main process
            $result['data'] = [];
            $result['data']['list'] = Ticker::getTickerList();
            $result['data']['account'] = $this->account === null ? UserAccount::getAllAccounts($this->user_id) : [];
            $result['status'] = 'ok';

            LogManager::addActionLog($this->user_id, 'Trade', 'buyItemForm', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Trade', 'buyItemForm', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get data for sell item form
     * 
     * @return type
     */
    public function sellItemForm() {
        // Data validation
        if ($this->account === null) {
            LogManager::addActionLog($this->user_id, 'Trade', 'sellItemForm', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], 'Invalid input data');
            return Response::json([
                        'error' => true,
                        'message' => 'Can not get data',
                        'code' => 500
                            ], 500);
        }

        try {
            // Main process
            $result['data'] = [];
            $result['data']['list'] = AccountBuyItem::getItemAmountGroupByTicker($this->account->id);
            $result['status'] = 'ok';

            LogManager::addActionLog($this->user_id, 'Trade', 'sellItemForm', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Trade', 'sellItemForm', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Add fund to account
     * 
     * @return type
     */
    public function addFund() {
        $amount = Input::has('amount') ? Input::get('amount') : 0;

        // Data validation
        if ($amount === 0 || $this->account === null) {
            LogManager::addActionLog($this->user_id, 'Trade', 'addFund', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], 'Invalid input data');
            return Response::json([
                        'error' => true,
                        'message' => 'Can not add fund',
                        'code' => 500
                            ], 500);
        }

        // Main process
        try {
            // Start transaction
            DB::beginTransaction();

            // Change money
            $money_before = $this->account->money;
            $this->account->money += $amount;
            $this->account->save();

            // Add transaction log
            LogManager::addTradeLog($this->account->id, TradeManager::$ACTION['Fund'], '', $amount, 1, $money_before, $this->account->money);
            $result['status'] = 'ok';

            // Commit transaction
            DB::commit();

            LogManager::addActionLog($this->user_id, 'Trade', 'addFund', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $this->account->id . ' - '. $amount);
            return Response::json($result);
        } catch (\Exception $e) {
            // Rollback transaction if error
            DB::rollback();
            LogManager::addActionLog($this->user_id, 'Trade', 'addFund', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Can not add fund. Server error: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Add buy record
     * 
     * @return type
     */
    public function buyItem() {
        $ticker = Input::has('ticker') ? Input::get('ticker')['ticker'] : '';
        $price = Input::has('price') ? Input::get('price') : 0;
        $amount = Input::has('amount') ? Input::get('amount') : 0;

        // Data validation
        if ($amount <= 0 || $price <= 0 || $this->account === null || !Ticker::checkTicker($ticker)) {
            LogManager::addActionLog($this->user_id, 'Trade', 'buyItem', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], 'Invalid input data');
            return Response::json([
                        'error' => true,
                        'message' => 'Can not add buy log',
                        'code' => 500
                            ], 500);
        }

        // Main process
        try {
            // Start transaction
            DB::beginTransaction();

            // Change money
            $money_before = $this->account->money;
            $this->account->money -= $amount * $price;
            $this->account->save();

            // Add transaction log
            $transaction = LogManager::addTradeLog($this->account->id, TradeManager::$ACTION['Buy'], $ticker, $price, $amount, $money_before, $this->account->money);
            AccountBuyItem::buyItem($this->account->id, $ticker, $price, $amount, $transaction->id, $transaction->date);
            $result['status'] = 'ok';

            // Commit transaction
            DB::commit();

            LogManager::addActionLog($this->user_id, 'Trade', 'buyItem', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $this->account->id . ' - '. $ticker);
            return Response::json($result);
        } catch (\Exception $e) {
            // Rollback transaction if error
            DB::rollback();
            LogManager::addActionLog($this->user_id, 'Trade', 'buyItem', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Can not add buy log. Server error: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Add sell record
     * 
     * @return type
     */
    public function sellItem() {
        $ticker = Input::has('ticker') ? Input::get('ticker')['ticker'] : '';
        $price = Input::has('price') ? Input::get('price') : 0;
        $amount = Input::has('amount') ? Input::get('amount') : 0;

        // Data validation
        if ($amount <= 0 || $price <= 0 || $this->account === null || !Ticker::checkTicker($ticker)) {
            LogManager::addActionLog($this->user_id, 'Trade', 'sellItem', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], 'Invalid input data');
            return Response::json([
                        'error' => true,
                        'message' => 'Can not add sell log',
                        'code' => 500
                            ], 500);
        }

        // Data validation
        $current = AccountBuyItem::getItemAmount($this->account->id, $ticker);
        $remain = 0;
        foreach ($current as $c) {
            $remain += $c->remain;
        }
        if ($amount > $remain) {
            LogManager::addActionLog($this->user_id, 'Trade', 'sellItem', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], 'Remain not enough');
            return Response::json([
                        'error' => true,
                        'message' => 'Remain amount not enough',
                        'code' => 500
                            ], 500);
        }

        // Main process
        try {
            // Start transaction
            DB::beginTransaction();

            // Change money
            $money_before = $this->account->money;
            $this->account->money += $amount * $price;
            $this->account->save();

            // Add transaction log
            $transaction = LogManager::addTradeLog($this->account->id, TradeManager::$ACTION['Sell'], $ticker, $price, $amount, $money_before, $this->account->money);
            $sellItem = AccountSellItem::sellItem($this->account->id, $ticker, $price, $amount, $transaction->id, $transaction->date);

            // Change item
            foreach ($current as $c) {
                if ($amount > $c->remain) {
                    AccountSellBuy::addTradeLog($this->account->id, $ticker, $c->price, $price, $c->remain, $c->id, $sellItem->id, $transaction->date);
                    $amount -= $c->remain;
                    $c->remain = 0;
                    $c->value = 0;
                    $c->save();
                } else {
                    AccountSellBuy::addTradeLog($this->account->id, $ticker, $c->price, $price, $amount, $c->id, $sellItem->id, $transaction->date);
                    $c->remain -= $amount;
                    $c->value = $c->remain * $c->price;
                    $c->save();
                    $amount = 0;
                    break;
                }
            }

            $result['status'] = 'ok';

            // Commit transaction
            DB::commit();

            LogManager::addActionLog($this->user_id, 'Trade', 'sellItem', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $this->account->id . ' - '. $ticker);
            return Response::json($result);
        } catch (\Exception $e) {
            // Rollback transaction if error
            DB::rollback();
            LogManager::addActionLog($this->user_id, 'Trade', 'sellItem', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Can not add sell log. Server error: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

}
