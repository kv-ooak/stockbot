<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTickerChartsTablie extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ticker_charts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')
                    ->unsigned()
                    ->nullable();
            $table->char('name', 50);
            $table->char('symbol', 12);
            $table->char('resolution', 12);
            $table->longtext('content', 50000);
            $table->timestamps();
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('ticker_charts');
    }

}
