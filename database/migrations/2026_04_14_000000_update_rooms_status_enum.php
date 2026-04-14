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
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('available');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('status', ['available', 'unavailable', 'occupied', 'reserved'])
                ->default('available')
                ->after('price_per_night');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('available')->default(true)->after('price_per_night');
        });
    }
};
