<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\UserAccount
 *
 * @property-read \App\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountBuyItem[] $buy_items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountSellItem[] $sell_items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountSellBuy[] $sell_buy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AccountTransactionLog[] $transaction_log
 * @property integer $id
 * @property integer $user_id
 * @property string $account_name
 * @property string $account_description
 * @property integer $money
 * @property integer $main_account
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $delete_flag
 */
class UserAccount extends Model {

    protected $table = 'user_accounts';

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function buy_items() {
        return $this->hasMany(AccountBuyItem::class);
    }

    public function sell_items() {
        return $this->hasMany(AccountSellItem::class);
    }

    public function sell_buy() {
        return $this->hasMany(AccountSellBuy::class);
    }

    public function transaction_log() {
        return $this->hasMany(AccountTransactionLog::class);
    }

    public static function getAccount($id, $user_id) {
        $data = new UserAccount;
        return $data::where('user_id', $user_id)
                        ->where('id', $id)
                        ->where('delete_flag', 0)
                        ->first();
    }

    public static function getAllAccounts($user_id) {
        $data = new UserAccount;
        return $data::where('user_id', $user_id)
                        ->where('delete_flag', 0)
                        ->get();
    }

}
