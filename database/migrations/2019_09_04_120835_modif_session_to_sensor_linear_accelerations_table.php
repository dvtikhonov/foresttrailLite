<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifSessionToSensorLinearAccelerationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('sensor_linear_accelerations', function (Blueprint $table) {
            $table->integer('tracker_sessions_id',false,true)->nullable();
            $table->dropForeign('sensor_linear_accelerations_user_id_foreign');
            $table->foreign('tracker_sessions_id')->references('id')->on('tracker_sessions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sensor_linear_accelerations', function (Blueprint $table) {
            //
        });
    }
}
