<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_reference')->unique()->nullable()->after('id');
            $table->decimal('total_price', 10, 2)->nullable()->after('guest_count');
            $table->enum('payment_status', ['pending', 'paid'])->default('pending')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['booking_reference', 'total_price', 'payment_status']);
        });
    }
};
