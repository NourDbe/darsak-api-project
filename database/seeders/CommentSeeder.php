<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * إضافة تعليقات تجريبية لطالب مسجل في كورس منشور.
     */
    public function run(): void
    {
        // اختيار طالب تجريبي محدد بدلاً من الاعتماد على أول طالب.
        $student = User::where('email', 'student1@darsak.test')
            ->where('role', 'student')
            ->first();

        if (!$student) {
            $this->command->warn(
                'Student not found. Run UserSeeder first.'
            );

            return;
        }

        // جلب الكورس المنشور الذي سنستخدمه في البيانات التجريبية.
        $course = Course::where('title', 'مقدمة في البرمجة')
            ->where('status', 'published')
            ->first();

        if (!$course) {
            $this->command->warn(
                'Published course not found. Run CourseSeeder first.'
            );

            return;
        }

        // تسجيل الطالب في الكورس دون تكرار العلاقة.
        $student->enrolledCourses()->syncWithoutDetaching([
            $course->id,
        ]);

        // تحديد المحتوى المرتبط بالكورس نفسه.
        $targets = [
            [
                'model' => $course,
                'body' => 'شرح جميل وسهل الفهم.',
            ],
            [
                'model' => Lesson::where('course_id', $course->id)
                    ->where('title', 'ما هي البرمجة؟')
                    ->first(),
                'body' => 'أصبح مفهوم البرمجة أوضح بالنسبة لي.',
            ],
            [
                'model' => Assignment::where('course_id', $course->id)
                    ->where('title', 'الواجب الأول: أساسيات البرمجة')
                    ->first(),
                'body' => 'الواجب مفيد لتطبيق المعلومات.',
            ],
        ];

        foreach ($targets as $target) {
            $content = $target['model'];

            // تخطي المحتوى غير الموجود مع إظهار تنبيه.
            if (!$content) {
                $this->command->warn(
                    'Comment target not found. Check LessonSeeder and AssignmentSeeder.'
                );

                continue;
            }

            // إنشاء التعليق عبر العلاقة متعددة الأشكال دون تكراره.
            $content->comments()->firstOrCreate(
                [
                    'user_id' => $student->id,
                    'body' => $target['body'],
                ]
            );
        }
    }
}
