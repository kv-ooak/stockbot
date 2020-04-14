<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTransactionLogTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('account_transaction_log', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->integer('account_id')->unsigned();
            $table->integer('action')->unsigned();
            $table->char('ticker', 8)->nullable();
            $table->float('price');
            $table->integer('amount')->unsigned();
            $table->float('money_before', 14, 2);
            $table->float('money_after', 14, 2);
            $table->timestamps();
            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('account_transaction_log');
    }

}
