<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLatLngColumnInOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->decimal('lat', 10, 8)->default('25.2048493')->after('address2');
            $table->decimal('lng', 11, 8)->default('55.270782800000006')->after('address2');
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
            $table->string('latitude')->default('25.2048493')->after('address2');
            $table->string('longitude')->default('55.270782800000006')->after('address2');
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng']);
        });        
    }
}
