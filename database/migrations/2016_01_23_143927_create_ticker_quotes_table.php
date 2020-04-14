<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTickerQuotesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ticker_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->char('ticker', 8);
            $table->date('date');
            $table->time('hour');
            $table->float('bid')->unsigned();
            $table->float('price')->unsigned();
            $table->float('ask')->unsigned();
            $table->integer('volume')->unsigned();
            $table->integer('total_volume')->unsigned();
            $table->char('status', 4);
            $table->float('value', 14, 2)->unsigned()->nullable();
            $table->timestamps();
            $table->index('date');
            $table->index('ticker');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('ticker_quotes');
    }

}
