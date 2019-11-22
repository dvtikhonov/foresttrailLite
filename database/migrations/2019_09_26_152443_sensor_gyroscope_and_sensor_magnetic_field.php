<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SensorGyroscopeAndSensorMagneticField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor_gyroscope', function (Blueprint $table) {
            $table->increments('id');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->integer('user_id',false,true)->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('tracker_sessions_id',false,true)->nullable();
            $table->foreign('tracker_sessions_id')->references('id')->on('tracker_sessions');
            $table->integer('interval')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sensor_magnetic_field', function (Blueprint $table) {
            $table->increments('id');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->integer('user_id',false,true)->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('tracker_sessions_id',false,true)->nullable();
            $table->foreign('tracker_sessions_id')->references('id')->on('tracker_sessions');
            $table->integer('interval')->default(0);
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
        Schema::dropIfExists('sensor_gyroscope');
        Schema::dropIfExists('sensor_magnetic_field');
    }
}
