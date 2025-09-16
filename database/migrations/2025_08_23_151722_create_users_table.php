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
            $table->string('email', 50)->unique();
            $table->string('name', 20)->unique();
            $table->string('phone', 100)->nullable();
            $table->string('password');
            $table->string('role', 1)->default('u');
            $table->string('stat', 1)->default('a');
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
