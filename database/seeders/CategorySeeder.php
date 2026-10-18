<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * إنشاء تصنيفات تجريبية وربطها بمحتوى تعليمي مختلف.
     */
    public function run(): void
    {
        // تعريف التصنيفات التي سنضيفها إلى قاعدة البيانات.
        $categories = [
            [
                'name' => 'البرمجة',
                'slug' => 'programming',
            ],
            [
                'name' => 'تطوير الويب',
                'slug' => 'web-development',
            ],
            [
                'name' => 'قواعد البيانات',
                'slug' => 'databases',
            ],
        ];

        // إنشاء التصنيفات دون تكرارها عند إعادة تشغيل Seeder.
        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                ['name' => $categoryData['name']]
            );
        }

        // ربط تصنيف البرمجة بالكورس المناسب.
        $programming = Category::where('slug', 'programming')->first();

        $course = Course::where('title', 'مقدمة في البرمجة')->first();

        if ($programming && $course) {
            // المزامنة تضيف العلاقة دون تكرارها في جدول الربط.
            $course->categories()->syncWithoutDetaching([$programming->id]);
        }

        // ربط تصنيف تطوير الويب بدرس Laravel.
        $webDevelopment = Category::where(
            'slug',
            'web-development'
        )->first();

        $lesson = Lesson::where('title', 'مقدمة إلى Laravel')->first();

        if ($webDevelopment && $lesson) {
            $lesson->categories()->syncWithoutDetaching([
                $webDevelopment->id,
            ]);
        }

        // ربط تصنيف قواعد البيانات بالواجب المناسب.
        $databases = Category::where('slug', 'databases')->first();

        $assignment = Assignment::where(
            'title',
            'الواجب الأول: استعلامات SQL'
        )->first();

        if ($databases && $assignment) {
            $assignment->categories()->syncWithoutDetaching([
                $databases->id,
            ]);
        }
    }
}
