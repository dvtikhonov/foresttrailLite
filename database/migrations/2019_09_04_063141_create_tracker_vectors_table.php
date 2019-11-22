<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackerVectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracker_vectors', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->integer('tracker_sessions_id',false,true)->nullable();
            $table->foreign('tracker_sessions_id')->references('id')->on('tracker_sessions');
            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracker_vectors', function (Blueprint $table) {
            //
        });
    }
}
