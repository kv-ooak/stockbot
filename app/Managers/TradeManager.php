<?php

namespace App\Managers;

use App\UserAccount;
use App\AccountBuyItem;
use App\AccountSellBuy;
use App\AccountSellItem;

class TradeManager {

    public static $ACTION = array(
        'Unknown' => 0,
        'Fund' => 1,
        'Buy' => 2,
        'Sell' => 3,
    );

}
