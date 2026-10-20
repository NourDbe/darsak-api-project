<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * إضافة دروس تجريبية وربطها بالكورسات.
     */
    public function run(): void
    {
        // تضمين الكورسات المنشورة والمسودات أثناء تعبئة بيانات الاختبار.
         $courses = Course::withoutGlobalScopes()->get();

        // إذا ما في كورسات، ما فينا نضيف دروس مرتبطة فيها.
        if ($courses->isEmpty()) {
            $this->command->warn(
                'No courses found. Seed courses before lessons.'
            );

            return;
        }

        // تعريف الدروس التجريبية، مع تحديد الكورس الذي ينتمي إليه كل درس.
        $lessons = [
            [
                'course_title' => 'مقدمة في البرمجة',
                'title' => 'ما هي البرمجة؟',
                'description' => 'التعرف على مفهوم البرمجة وأهميتها.',
                'video_url' => 'https://www.youtube.com/watch?v=example01',
                'sort_order' => 1,
            ],
            [
                'course_title' => 'مقدمة في البرمجة',
                'title' => 'المتغيرات وأنواع البيانات',
                'description' => 'التعرف على المتغيرات وأنواع البيانات الأساسية.',
                'video_url' => 'https://www.youtube.com/watch?v=example02',
                'sort_order' => 2,
            ],
            [
                'course_title' => 'تطوير تطبيقات الويب باستخدام Laravel',
                'title' => 'مقدمة إلى Laravel',
                'description' => 'التعرف على إطار العمل وبنية المشروع.',
                'video_url' => 'https://www.youtube.com/watch?v=example03',
                'sort_order' => 1,
            ],
            [
                'course_title' => 'أساسيات قواعد البيانات',
                'title' => 'مقدمة إلى SQL',
                'description' => 'التعرف على الاستعلامات الأساسية.',
                'video_url' => 'https://www.youtube.com/watch?v=example04',
                'sort_order' => 1,
            ],
        ];

        // المرور على الدروس لإنشاء كل درس وربطه بالكورس المناسب.
        foreach ($lessons as $lessonData) {

            // البحث عن الكورس باستخدام عنوانه.
            $course = $courses->firstWhere(
                'title',
                $lessonData['course_title']
            );

            // تخطي الدرس إذا لم نجد الكورس المطلوب.
            if (!$course) {
                $this->command->warn(
                    "Course not found: {$lessonData['course_title']}"
                );

                continue;
            }

            // إزالة عنوان الكورس من بيانات الدرس لأنه ليس عموداً في جدول lessons.
            unset($lessonData['course_title']);

            // إنشاء الدرس داخل الكورس المحدد، دون تكراره عند إعادة تشغيل Seeder.
            $course->lessons()->firstOrCreate(
                [
                    'title' => $lessonData['title'],
                ],
                $lessonData
            );
        }
    }
}
