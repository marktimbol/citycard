<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReservationsTable extends Migration
{    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function(Blueprint $table) {
            $table->dropColumn('services');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->integer('item_id')->nullable();
            $table->integer('quantity')->nullable()->after('time');
            $table->dateTime('date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('services')->nullable();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['item_id', 'quantity']);
            $table->string('date')->change();
        });
    }
}
