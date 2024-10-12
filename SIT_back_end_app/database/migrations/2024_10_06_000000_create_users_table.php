<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number', 15);
            $table->string('password');
            $table->bigInteger('group_id')->unsigned()->nullable();
            $table->enum('role', ['admin', 'staff', 'user'])->default('user');
            $table->timestamps();
            $table->foreign('group_id')->references('id')->on('groups');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
