<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressColumnInOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn(['address1', 'address2']);
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->string('address')->default('Dubai - United Arab Emirates')->after('currency');
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
            $table->string('address1')->default('Dubai')->after('currency');
            $table->string('address2')->default('UAE')->after('currency');
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn('address');
        });        
    }
}
