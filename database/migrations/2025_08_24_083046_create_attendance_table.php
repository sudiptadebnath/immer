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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->dateTime('scan_datetime'); // full timestamp of scan
            $table->foreignId('scan_by')->constrained('users')->onDelete('cascade'); // who scanned
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // user whose QR is scanned
            $table->string('post', 30)->default('post1'); // optional location
            $table->string('typ', 10)->default('queue');
            $table->string('location')->nullable(); // optional location
            $table->unique(['scan_by', 'user_id', 'post', 'typ']); // prevent duplicate scans at exact same time
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
