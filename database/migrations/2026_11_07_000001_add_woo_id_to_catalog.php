<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Eski WooCommerce kimlikleri: içe aktarma tekrar çalıştırıldığında aynı
 * kaydı bulmak ve eski /product/... adreslerini yeni sayfaya yönlendirmek için.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('woo_id')->nullable()->unique()->after('id');
            $table->string('woo_slug')->nullable()->index()->after('woo_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('woo_slug')->nullable()->index()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['woo_id']);
            $table->dropIndex(['woo_slug']);
            $table->dropColumn(['woo_id', 'woo_slug']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['woo_slug']);
            $table->dropColumn('woo_slug');
        });
    }
};
