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
            $table->dateTime('scan_datetime');
            $table->foreignId('scan_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('puja_committee_id')->constrained('puja_committees')->onDelete('cascade');
            $table->string('typ', 10)->default('queue');
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
