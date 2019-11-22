<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToProviderPos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_pos', function (Blueprint $table) {
            $table->double('lat')->nullable()->after('address');
            $table->double('lon')->nullable()->after('address');
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
        Schema::table('provider_pos', function (Blueprint $table) {
            $table->dropColumn('lat' );
            $table->dropColumn('lon' );
        });
    }
}
