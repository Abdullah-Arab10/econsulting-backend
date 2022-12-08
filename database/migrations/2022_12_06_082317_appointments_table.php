<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('appointments',function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('consultant_id');
            $table->dateTime('appointment_start');
            $table->dateTime('appointment_end');
            $table->foreign('client_id')->references('user_id')->on('normal_users')->onDelete('cascade');
            $table->foreign('consultant_id')->references('user_id')->on('consultants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
        //
    }
};
