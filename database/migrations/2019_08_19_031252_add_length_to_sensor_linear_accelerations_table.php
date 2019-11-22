<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLengthToSensorLinearAccelerationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('sensor_linear_accelerations', 'S_x')) {
            Schema::table('sensor_linear_accelerations',function (Blueprint $table) {
                $table->float('S_x')->nullable();
            });
            Schema::table('sensor_linear_accelerations', function (Blueprint $table){
                $table->string('S_x')->nullable(false)->change();
            });
        }
        if (!Schema::hasColumn('sensor_linear_accelerations', 'S_y')) {
            Schema::table('sensor_linear_accelerations',function (Blueprint $table) {
                $table->float('S_y')->nullable();
            });
            Schema::table('sensor_linear_accelerations', function (Blueprint $table){
                $table->string('S_y')->nullable(false)->change();
            });
        }
        if (!Schema::hasColumn('sensor_linear_accelerations', 'S_z')) {
            Schema::table('sensor_linear_accelerations',function (Blueprint $table) {
                $table->float('S_z')->nullable();
            });
            Schema::table('sensor_linear_accelerations', function (Blueprint $table){
                $table->string('S_z')->nullable(false)->change();
            });
        }
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
            $table->dropColumn('S_x');
            $table->dropColumn('S_y');
            $table->dropColumn('S_z');
        });
    }
}
