<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('S_x', 'S_y', 'S_z', 'session')) {
            Schema::table('sensor_linear_accelerations', function (Blueprint $table) {
                $table->dropColumn(['S_x', 'S_y', 'S_z', 'session']);
            });
        }
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
