<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reward_id');
            $table->string('url');
            $table->string('thumbnail');

            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('cascade');
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
        Schema::dropIfExists('reward_photos');
    }
}
