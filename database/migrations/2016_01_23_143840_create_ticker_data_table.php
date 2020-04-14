<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTickerDataTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ticker_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticker_id')
                    ->unsigned()
                    ->nullable();
            $table->char('ticker', 10);
            $table->date('date');
            $table->float('open')->unsigned();
            $table->float('high')->unsigned();
            $table->float('low')->unsigned();
            $table->float('close')->unsigned();
            $table->integer('volume')->unsigned();
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
        Schema::drop('ticker_data');
    }

}
