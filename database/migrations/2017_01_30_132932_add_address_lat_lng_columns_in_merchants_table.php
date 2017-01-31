<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressLatLngColumnsInMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('address')->default('Dubai - United Arab Emirates')->after('phone');
            $table->decimal('lat', 10, 8)->default('25.2048493')->after('phone');
            $table->decimal('lng', 11, 8)->default('55.270782800000006')->after('phone');
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
            $table->dropColumn(['address', 'lat', 'lng']);
        });
    }
}
