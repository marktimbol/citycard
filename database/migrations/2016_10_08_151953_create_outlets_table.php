<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id')->unsigned();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            $table->string('address1');
            $table->string('address2');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('type');
            $table->string('country');
            $table->string('city');
            $table->string('area');
            $table->foreign('merchant_id')
                  ->references('id')->on('merchants')
                  ->onDelete('cascade');
            $table->string('api_token');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outlets');
    }
}
