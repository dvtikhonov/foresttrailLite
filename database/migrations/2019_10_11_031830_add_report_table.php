<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('devices_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id',false,true)->nullable();
            $table->foreign('device_id')->references('id')->on('devices');
            $table->integer('minutes')->default(0);
            $table->double('tariff_amount')->default(0);
            $table->double('total')->default(0);
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
//        Schema::dropIfExists('devices_report');
    }
}
