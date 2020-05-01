<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('mobile_number')->unique()->nullable();
            $table->boolean('status')->nullable();
            $table->string('password'); 
            $table->string('type')->default('user');
            $table->boolean('verified')->default(false);
            $table->string('referral_code')->nullable()->unique();
            $table->integer('referrals')->default(0);
            $table->string('referred_by_id')->nullable();
            $table->mediumText('img_url')->nullable();
            $table->string('business_name')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('location')->nullable();
            $table->boolean('booking_status')->default(0);
            $table->string('completed_by')->nullable();
            $table->boolean('availability')->default(0);
            $table->string('device_type')->nullable();
            $table->text('device_token')->nullable();
            /**
             * true means vendor is online and false means  they're offline.
             *  Offline => available to be booked
             */
            $table->softDeletes();           
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
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('users');
    }
}
