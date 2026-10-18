<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة أعمدة الربط الناقصة دون حذف البيانات الموجودة.
     */
    public function up(): void
    {
        Schema::table('categorizables', function (Blueprint $table) {
            // ربط التصنيف بالمحتوى
            if (!Schema::hasColumn('categorizables', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->constrained('categories')
                    ->cascadeOnDelete();
            }

            // معرّف المحتوى المرتبط بالتصنيف
            if (!Schema::hasColumn('categorizables', 'categorizable_id')) {
                $table->unsignedBigInteger('categorizable_id')->nullable();
            }

            // نوع المحتوى: course أو lesson أو assignment
            if (!Schema::hasColumn('categorizables', 'categorizable_type')) {
                $table->string('categorizable_type')->nullable();
            }
        });
    }

    /**
     * لا نحذف الأعمدة عند التراجع، حفاظاً على البيانات.
     */
    public function down(): void
    {
        // لا شيء
    }
};
