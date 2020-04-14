<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AccountSellBuy
 *
 * @property integer $id
 * @property string $sell_date
 * @property integer $account_id
 * @property string $ticker
 * @property float $buy_price
 * @property float $sell_price
 * @property integer $amount
 * @property integer $buy_id
 * @property integer $sell_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\UserAccount $account
 */
class AccountSellBuy extends Model {

    protected $table = 'account_sell_buy';

    public function account() {
        return $this->belongsTo(UserAccount::class);
    }

    public static function getLog($account_id) {
        $data = new AccountSellBuy;
        return $data::where('account_id', $account_id)
                        ->orderBy('sell_date', 'desc')
                        ->get();
    }

    public static function addTradeLog($account_id, $ticker, $buy_price, $sell_price, $amount, $buy_id, $sell_id, $date) {
        $log = new AccountSellBuy;
        $log->account_id = $account_id;
        $log->ticker = $ticker;
        $log->sell_date = $date;
        $log->buy_price = $buy_price;
        $log->sell_price = $sell_price;
        $log->amount = $amount;
        $log->buy_id = $buy_id;
        $log->sell_id = $sell_id;
        $log->save();

        return $log;
    }

}
