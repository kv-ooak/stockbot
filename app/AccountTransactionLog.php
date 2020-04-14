<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AccountTransactionLog
 *
 * @property integer $id
 * @property string $date
 * @property integer $account_id
 * @property integer $action
 * @property string $ticker
 * @property float $price
 * @property integer $amount
 * @property float $money_before
 * @property float $money_after
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\UserAccount $account
 */
class AccountTransactionLog extends Model {

    protected $table = 'account_transaction_log';

    public function account() {
        return $this->belongsTo(UserAccount::class);
    }

}
