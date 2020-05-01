<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('user_id')->nullable();
            $table->string('booking_type')->nullable();
            $table->enum('status',['pending','accepted','rejected'])->default('pending');
            $table->float('base_price',10,4)->default(0);
            $table->string('discount')->nullable();
            $table->float('net_price',10,4)->default(0);
            $table->integer('no_of_rooms')->default(0);
            $table->string('timestamp')->nullable();
            $table->string('location')->nullable();
            $table->boolean('paid')->default(0);
            $table->boolean('done')->default(0);
            $table->string('schedule')->default('once');
            $table->string('address')->nullable();
            $table->string('time')->nullable();
            $table->string('start_date')->nullable();
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
        Schema::dropIfExists('bookings');
    }
}
