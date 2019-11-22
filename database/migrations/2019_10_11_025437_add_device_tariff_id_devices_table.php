<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeviceTariffIdDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('devices', function (Blueprint $table) {
//            $table->engine = 'InnoDB';
            $table->integer('device_tariff_id',false,true)->nullable()->after('name');
            $table->foreign('device_tariff_id')->references('id')->on('device_tariffs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {

//            $table->dropColumn('device_tariff_id');

        });
    }
}
