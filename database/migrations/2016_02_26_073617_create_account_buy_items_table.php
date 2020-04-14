<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountBuyItemsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('account_buy_items', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('buy_date');
            $table->integer('account_id')->unsigned();
            $table->char('ticker', 8);
            $table->float('price')->unsigned();
            $table->integer('amount')->unsigned();
            $table->integer('remain')->unsigned();
            $table->float('value', 14, 2)->unsigned();
            $table->integer('transaction_id');
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
        Schema::drop('account_buy_items');
    }

}
