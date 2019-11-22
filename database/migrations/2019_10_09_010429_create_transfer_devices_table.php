<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class  CreateTransferDevicesTable    extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id', false, true)->nullable();
            $table->integer('provider_id', false, true)->nullable();
            $table->dateTime('transferred_at')->nullable();
            $table->dateTime('returned_at')->nullable();
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
        Schema::dropIfExists('transfer_devices');
    }
}
