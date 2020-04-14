<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTickerRecommendsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ticker_recommends', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticker_id')
                    ->unsigned();
            $table->date('date');
            $table->float('price')->unsigned();
            $table->char('indicator', 12);
            $table->char('ticker', 8);
            $table->char('signal', 20);
            $table->float('avg_volume_20', 12, 0)
                    ->unsigned();
            $table->float('net_buy', 12, 0)
                    ->nullable();
            $table->float('net_buy_value', 14, 2)
                    ->nullable();
            $table->char('strength', 3);
            $table->char('arrow', 7);
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
        Schema::drop('ticker_recommends');
    }

}
