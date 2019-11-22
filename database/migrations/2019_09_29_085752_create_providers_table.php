<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id',false,true)->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name',50)->nullable();
            $table->string('address',255)->nullable();
            $table->string('contacts',255)->nullable();
//            $table->integer('phone')->unsigned()->nullable();
            $table->bigInteger('phone')->nullable();
//            $table->string('phone',25)->nullable();
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
//        Schema::dropIfExists('providers');
    }
}
