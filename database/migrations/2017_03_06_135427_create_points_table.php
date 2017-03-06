<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('registration')->default(1000);
            $table->integer('login')->default(10);
            $table->integer('search')->default(1);
            $table->integer('invite_friend')->default(1000);
            $table->integer('complete_profile')->default(1000);
            $table->integer('link_facebook_account')->default(500);
            $table->integer('link_instagram_account')->default(500);
            $table->integer('reservation')->default(500);
            $table->integer('delivery')->default(500);
            $table->integer('add_to_wishlish')->default(50);
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
        Schema::dropIfExists('points');
    }
}
