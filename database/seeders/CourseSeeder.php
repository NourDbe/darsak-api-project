<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * تشغيل الـ Seeder لإضافة كورسات تجريبية.
     */
    public function run(): void
    {
        // جلب جميع المستخدمين الذين يمتلكون دور مدرس.
        $teachers = User::where('role', 'teacher')->get();

        // إذا لم يوجد أي مدرس، نتوقف حتى لا ننشئ كورسات بلا مدرس.
        if ($teachers->isEmpty()) {
            $this->command->warn(
                'No teachers found. Seed teachers before courses.'
            );

            return;
        }

        // تعريف مجموعة من الكورسات التجريبية التي سنضيفها إلى قاعدة البيانات.
        $courses = [
            [
                'title' => 'مقدمة في البرمجة',
                'description' => 'تعلم أساسيات البرمجة من الصفر.',
                'level' => 'beginner',
                'status' => 'published',
                'is_free' => true,
                'price' => null,
            ],
            [
                'title' => 'تطوير تطبيقات الويب باستخدام Laravel',
                'description' => 'تعلم بناء تطبيقات ويب باستخدام Laravel.',
                'level' => 'intermediate',
                'status' => 'published',
                'is_free' => false,
                'price' => 25,
            ],
            [
                'title' => 'أساسيات قواعد البيانات',
                'description' => 'مقدمة في SQL وتصميم قواعد البيانات.',
                'level' => 'beginner',
                'status' => 'draft',
                'is_free' => true,
                'price' => null,
            ],
        ];

        // المرور على الكورسات واحداً تلو الآخر، مع الحصول على رقم كل كورس.
        foreach ($courses as $index => $courseData) {

            // توزيع الكورسات على المدرسين الموجودين.
            // استخدام باقي القسمة يسمح بتكرار المدرسين عند الحاجة.
            $teacher = $teachers[$index % $teachers->count()];

            // إنشاء الكورس إذا لم يكن موجوداً مسبقاً للمدرس نفسه.
            // إذا كان موجوداً، فلن ننشئ نسخة مكررة منه.
            Course::firstOrCreate(
                [
                    'title' => $courseData['title'],
                    'teacher_id' => $teacher->id,
                ],
                $courseData
            );
        }
    }
}
