<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_attributes', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('attribute_name_id');
            $table->string('measurement');
            $table->float('price',10,4);
            $table->string('image_icons');
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
        Schema::dropIfExists('service_attributes');
    }
}
