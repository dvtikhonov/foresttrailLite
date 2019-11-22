<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTransferDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_devices', function (Blueprint $table) {
            $table->dropColumn('provider_id');
            $table->integer('provider_pos_id',false,true)->nullable()->after('device_id');
            $table->foreign('provider_pos_id')->references('id')->on('provider_pos');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_devices', function (Blueprint $table) {
            //
        });
    }
}
