<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrencyColumnInMerchantsAndOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('currency')->after('phone')->nullable();
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->string('currency')->after('phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
}
