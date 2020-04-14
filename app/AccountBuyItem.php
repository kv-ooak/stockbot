<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AccountBuyItem
 *
 * @property integer $id
 * @property string $buy_date
 * @property integer $account_id
 * @property string $ticker
 * @property float $price
 * @property integer $amount
 * @property integer $remain
 * @property float $value
 * @property integer $transaction_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\UserAccount $account
 */
class AccountBuyItem extends Model {

    protected $table = 'account_buy_items';

    public function account() {
        return $this->belongsTo(UserAccount::class);
    }

    public static function buyItem($account_id, $ticker, $price, $amount, $transaction_id, $date) {
        $item = new AccountBuyItem;
        $item->account_id = $account_id;
        $item->ticker = $ticker;
        $item->price = $price;
        $item->amount = $amount;
        $item->remain = $amount;
        $item->value = $amount * $price;
        $item->transaction_id = $transaction_id;
        $item->buy_date = $date;
        $item->save();

        return $item;
    }

    public static function getItemAmount($acount_id, $ticker) {
        $item = new AccountBuyItem;
        return $item::where('account_id', $acount_id)
                        ->where('ticker', $ticker)
                        ->where('remain', '>', 0)
                        ->orderBy('buy_date', 'asc')
                        ->get();
    }

    public static function getItemAmountGroupByTicker($account_id) {
        $item = new AccountBuyItem;
        return $item::selectRaw('ticker, sum(remain) as remains, sum(value) as "values"')
                        ->where('account_id', $account_id)
                        ->where('remain', '>', 0)
                        ->groupBy('ticker')
                        ->orderby('ticker', 'asc')
                        ->get();
    }

}
