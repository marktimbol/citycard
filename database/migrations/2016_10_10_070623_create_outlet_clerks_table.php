<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutletClerksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlet_clerks', function (Blueprint $table) {
            $table->integer('outlet_id')->unsigned();
            $table->integer('clerk_id')->unsigned();

            $table->foreign('outlet_id')
                ->references('id')->on('outlets')
                ->onDelete('cascade');

            $table->foreign('clerk_id')
                ->references('id')->on('clerks')
                ->onDelete('cascade');
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
        Schema::dropIfExists('outlet_clerks');
    }
}
