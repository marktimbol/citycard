<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutletPromosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlet_promos', function (Blueprint $table) {
            $table->integer('outlet_id')->unsigned();
            $table->integer('promo_id')->unsigned();

            $table->foreign('outlet_id')
                ->references('id')->on('outlets')
                ->onDelete('cascade');

            $table->foreign('promo_id')
                ->references('id')->on('promos')
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
        Schema::dropIfExists('outlet_promos');
    }
}
