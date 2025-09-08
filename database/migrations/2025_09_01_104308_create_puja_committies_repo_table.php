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
        Schema::create('puja_committies_repo', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->unsignedBigInteger('action_area_id')->nullable()->index();
            $table->unsignedBigInteger('puja_category_id')->nullable()->index();

            // Main fields
            $table->string('name', 200)->unique();
            $table->text('puja_address')->nullable();
            $table->unsignedInteger('view_order')->default(0)->index();

            // Constraints
            $table->foreign('action_area_id')
                  ->references('id')->on('action_areas')
                  ->onDelete('set null');

            $table->foreign('puja_category_id')
                  ->references('id')->on('puja_categories')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puja_committies_repo');
    }
};
