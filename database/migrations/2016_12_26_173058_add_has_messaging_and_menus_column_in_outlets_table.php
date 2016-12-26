<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasMessagingAndMenusColumnInOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->boolean('has_messaging')->default(false)->after('has_reservation');
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->boolean('has_menus')->default(false)->after('has_messaging');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn(['has_messaging', 'has_menus']);
        });
    }
}
