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
            $table->id();
            $table->string('uid', 20)->unique();
            $table->string('name', 150)->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('mob', 20)->nullable()->unique();
            $table->string('password'); 
            $table->string('role', 1)->default('u'); 
            $table->string('stat', 1)->default('a'); 
            $table->string('token', 64)->nullable()->unique(); 
            $table->timestamps();
            $table->timestamp('logged_at')->nullable();
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
