<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTickerBotsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ticker_bots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticker_id')
                    ->unsigned()
                    ->nullable();
            $table->char('ticker', 8)->index();
            $table->date('date')->orderBy('desc');
            $table->float('avg_volume_20', 12, 0);
            $table->float('EMA9', 5, 1);
            $table->float('MA20', 5, 1);
            $table->float('MA42', 5, 1);
            $table->float('MACD', 3, 1);
            $table->float('MACDSignal', 3, 1);
            $table->float('RSI', 4, 1);
            $table->float('SAR', 5, 1);
            $table->float('UpperBB', 5, 1);
            $table->float('LowerBB', 5, 1);
            $table->float('plusDI', 5, 1);
            $table->float('minusDI', 5, 1);
            $table->float('ADX', 4, 1);
            $table->float('CCI', 5, 1);
            $table->index(array('ticker', 'date', 'avg_volume_20'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('ticker_bots');
    }

}
