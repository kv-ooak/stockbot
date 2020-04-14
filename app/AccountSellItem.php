<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AccountSellItem
 *
 * @property integer $id
 * @property string $sell_date
 * @property integer $account_id
 * @property string $ticker
 * @property float $price
 * @property integer $amount
 * @property integer $transaction_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\UserAccount $account
 */
class AccountSellItem extends Model {

    protected $table = 'account_sell_items';

    public function account() {
        return $this->belongsTo(UserAccount::class);
    }

    public static function sellItem($account_id, $ticker, $price, $amount, $transaction_id, $date) {
        $item = new AccountSellItem;
        $item->account_id = $account_id;
        $item->ticker = $ticker;
        $item->price = $price;
        $item->amount = $amount;
        $item->transaction_id = $transaction_id;
        $item->sell_date = $date;
        $item->save();

        return $item;
    }

}
