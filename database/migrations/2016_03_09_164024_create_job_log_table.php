<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobLogTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('job_log', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->longText('action');
            $table->longText('param');
            $table->tinyInteger('status')->unsigned();
            $table->longText('comment');
            $table->timestamps();
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('job_log');
    }

}
