<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequesttsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requestts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('meetpointLatitude');
            $table->double('meetpointLongitude');
            $table->double('destinationLatitude');
            $table->double('destinationLongitude');
            $table->integer('neededSeats')->default(1);
            $table->time('time');
            $table->boolean('response')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->integer('ride_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requestts');
    }
}
