<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('product_id');
            $table->string('unit')->default('rooms');
            $table->integer('unit_count')->default(0);
            $table->float('one')->default(0);
            $table->float('two')->default(0);
            $table->float('three')->default(0);
            $table->float('four')->default(0);
            $table->float('five')->default(0);
            $table->float('six')->default(0);
            $table->float('seven')->default(0);
            $table->float('eight')->default(0);
            $table->float('nine')->default(0);
            $table->float('ten')->default(0);
            $table->float('default')->default(0);
            $table->softDeletes(); 
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
        Schema::dropIfExists('prices');
    }
}
