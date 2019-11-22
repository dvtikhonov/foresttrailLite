<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBlockDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_pos_block_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('provider_pos_id',false,true);
            $table->foreign('provider_pos_id')->references('id')->on('provider_pos');
            $table->dateTime('blocked_at')->nullable();
            $table->dateTime('restored_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('transfer_devices', function(Blueprint $table)
        {
            $table->foreign('device_id')->references('id')->on('devices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
