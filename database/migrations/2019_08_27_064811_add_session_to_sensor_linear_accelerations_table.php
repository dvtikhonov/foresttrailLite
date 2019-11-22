<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSessionToSensorLinearAccelerationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sensor_linear_accelerations', function (Blueprint $table) {
            //
            if (!Schema::hasColumn('sensor_linear_accelerations', 'session')) {
                Schema::table('sensor_linear_accelerations',function (Blueprint $table) {
                    $table->integer('session')->nullable();
                });
                Schema::table('sensor_linear_accelerations',function (Blueprint $table) {
                    $table->integer('session')->nullable(false)->change();
                });
            }

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
