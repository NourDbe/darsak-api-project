<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         // إنشاء المدرسين والطلاب أولاً.
        $this->call(UserSeeder::class);

        // إنشاء الكورسات بعد وجود المدرسين.
        $this->call(CourseSeeder::class);

        // إنشاء الدروس بعد وجود الكورسات.
        $this->call(LessonSeeder::class);

        // إنشاء الواجبات بعد إنشاء الكورسات.
        $this->call(AssignmentSeeder::class);

        // إضافة التعليقات بعد إنشاء المحتوى الذي ستُربط به.
        $this->call(CommentSeeder::class);
        // إضافة التصنيفات وربطها بالكورسات والدروس والواجبات.
        $this->call(CategorySeeder::class);

    }
}
