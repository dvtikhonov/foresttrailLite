<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderPosPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_pos_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('amount',false,true)->nullable();
            $table->string('description',255)->nullable();
            $table->integer('provider_pos_id',false,true)->nullable();
            $table->foreign('provider_pos_id')->references('id')->on('provider_pos');
            $table->integer('user_id',false,true)->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('operation_id',false,true)->nullable();
            $table->foreign('operation_id')->references('id')->on('operations');
            $table->integer('target_id',false,true)->nullable();
            $table->index('target_id');
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
        Schema::dropIfExists('provider_pos_payments');
    }
}
