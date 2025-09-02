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
        Schema::create('puja_committees', function (Blueprint $table) {
            $table->id();

            $table->string('action_area', 10)->nullable();
            $table->string('category', 10)->nullable();
            $table->string('puja_committee_name', 100)->nullable();
            $table->text('puja_committee_address')->nullable();
            $table->string('secretary_name', 100)->nullable();
            $table->string('secretary_mobile', 20)->nullable()->unique();
            $table->string('chairman_name', 100)->nullable();
            $table->string('chairman_mobile', 20)->nullable()->unique();
            $table->date('proposed_immersion_date')->nullable();
            $table->time('proposed_immersion_time')->nullable();
            $table->string('vehicle_no', 50)->nullable();
            $table->unsignedTinyInteger('team_members')->nullable();
            $table->string('stat', 1)->default('a');
            $table->string('token', 64)->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puja_committees');
    }
};
