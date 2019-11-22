<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToCoordinates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coordinates', function (Blueprint $table) {
            $table->integer('tracker_sessions_id',false,true)->nullable()->after('user_id');
            $table->foreign('tracker_sessions_id')->references('id')->on('tracker_sessions');
//            $table->integer('device_id',false,true)->nullable()->after('accuracy');
//            $table->foreign('device_id')->references('id')->on('devices');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coordinates', function (Blueprint $table) {
            $table->dropForeign('coordinates_tracker_sessions_id_foreign');
            $table->dropColumn('tracker_sessions_id' );
//            $table->dropForeign('coordinates_device_id_foreign');
//            $table->dropColumn('device_id');
        });
    }
}
