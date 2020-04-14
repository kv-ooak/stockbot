<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountSellBuyTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('account_sell_buy', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('sell_date');
            $table->integer('account_id')->unsigned();
            $table->char('ticker', 8);
            $table->float('buy_price')->unsigned();
            $table->float('sell_price')->unsigned();
            $table->integer('amount')->unsigned();
            $table->integer('buy_id')->unsigned();
            $table->integer('sell_id')->unsigned();
            $table->timestamps();
            $table->index('account_id');
            $table->index('ticker');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('account_sell_buy');
    }

}
