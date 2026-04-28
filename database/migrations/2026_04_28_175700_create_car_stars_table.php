<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_stars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unique(['car_id', 'user_id']);
            $table->timestamps();
        });

        // Add stars_count to cars table
        Schema::table('cars', function (Blueprint $table) {
            if (!Schema::hasColumn('cars', 'stars_count')) {
                $table->integer('stars_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_stars');
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('stars_count');
        });
    }
};
