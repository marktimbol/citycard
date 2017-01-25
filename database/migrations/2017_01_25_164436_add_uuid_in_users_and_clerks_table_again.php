<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUuidInUsersAndClerksTableAgain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->default('uuid')->after('id');
        });

        Schema::table('clerks', function (Blueprint $table) {
            $table->uuid('uuid')->default('uuid')->after('id');
        });     
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('clerks', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });   
    }
}
