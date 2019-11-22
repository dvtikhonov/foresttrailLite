<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_pos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('provider_id',false,true)->nullable();
            $table->foreign('provider_id')->references('id')->on('providers');
            $table->string('name',50)->nullable();
            $table->string('address',255)->nullable();
            $table->string('contacts',255)->nullable();
            $table->bigInteger('phone')->nullable();
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
//        Schema::dropIfExists('provider_pos');
    }
}
