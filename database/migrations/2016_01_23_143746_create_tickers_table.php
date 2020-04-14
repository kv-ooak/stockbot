<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTickersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tickers', function (Blueprint $table) {
            $table->increments('id');
            $table->char('ticker', 8);
            $table->char('exchange', 4);
            $table->integer('outstanding')->unsigned();
            $table->integer('listed')->unsigned();
            $table->integer('treasury')->unsigned();
            $table->integer('foreign_owned')->unsigned();
            $table->integer('equity')->unsigned();
            $table->index('ticker');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('tickers');
    }

}
