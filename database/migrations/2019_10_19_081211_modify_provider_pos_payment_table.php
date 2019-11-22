<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyProviderPosPaymentTable extends Migration
{
    /**
     * ошибка
     * SQLSTATE[HY000]: General error: 1215 Cannot add foreign key constraint
     *
     * необходимо проконтролировать совпадение:  длинны Integer и unsigned,
     * на внешних ключах и id таблиц.
     *
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::table('provider_pos_payments', function (Blueprint $table) {
            $table->dropForeign('provider_pos_payments_operation_id_foreign');
            $table->dropForeign('provider_pos_payments_provider_pos_id_foreign');
            $table->integer('provider_pos_id',false,true)->nullable($value = false)->change();
            $table->foreign('provider_pos_id')->references('id')->on('provider_pos');
            $table->integer('operation_id',false,true)->nullable($value = false)->change();
            $table->foreign('operation_id')->references('id')->on('operations');
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
