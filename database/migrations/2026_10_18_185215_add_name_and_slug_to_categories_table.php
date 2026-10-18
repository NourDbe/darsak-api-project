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
    // إضافة اسم التصنيف إذا كان العمود غير موجود.
    if (!Schema::hasColumn('categories', 'name')) {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name')->nullable()->unique();
        });
    }

    // إضافة slug إذا كان العمود غير موجود.
    if (!Schema::hasColumn('categories', 'slug')) {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
        });
    }
}

public function down(): void
{
    // حذف العمودين عند التراجع عن هذه Migration.
    Schema::table('categories', function (Blueprint $table) {
        $table->dropUnique(['name']);
        $table->dropUnique(['slug']);
        $table->dropColumn(['name', 'slug']);
    });
}

};
