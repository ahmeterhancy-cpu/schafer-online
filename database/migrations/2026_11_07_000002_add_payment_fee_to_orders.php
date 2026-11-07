<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Ödeme yöntemine bağlı ek bedel (ör. kapıda ödeme hizmet bedeli).
 * grand_total'a dahildir; ayrı tutulur ki faturada ve sipariş özetinde
 * ayrı satır olarak görünsün.
 *
 * payment_provider sütunu zaten var: paytr | havale | kapida
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('payment_fee', 10, 2)->default(0)->after('shipping_total');
            $table->index(['payment_provider', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_provider', 'payment_status']);
            $table->dropColumn('payment_fee');
        });
    }
};
