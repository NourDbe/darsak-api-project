<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Assignment;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    /**
     * إضافة واجبات تجريبية وربطها بالكورسات.
     */
    public function run(): void
    {

        // تضمين الكورسات المنشورة والمسودات أثناء تعبئة بيانات الاختبار.
        $courses = Course::withoutGlobalScopes()->get();

        // التوقف إذا لم توجد كورسات لإنشاء واجبات مرتبطة بها.
        if ($courses->isEmpty()) {
            $this->command->warn(
                'No courses found. Seed courses before assignments.'
            );

            return;
        }

        // تعريف الواجبات التجريبية وعناوين الكورسات التابعة لها.
        $assignments = [
            [
                'course_title' => 'مقدمة في البرمجة',
                'title' => 'الواجب الأول: أساسيات البرمجة',
                'description' => 'اكتب برنامجاً بسيطاً يشرح استخدام المتغيرات.',
                'due_at' => now()->addDays(7),
            ],
            [
                'course_title' => 'تطوير تطبيقات الويب باستخدام Laravel',
                'title' => 'الواجب الأول: أساسيات Laravel',
                'description' => 'أنشئ مساراً وController يعيد استجابة JSON.',
                'due_at' => now()->addDays(10),
            ],
            [
                'course_title' => 'أساسيات قواعد البيانات',
                'title' => 'الواجب الأول: استعلامات SQL',
                'description' => 'اكتب استعلامات لاستخراج البيانات من جدول.',
                'due_at' => now()->addDays(5),
            ],
        ];

        // المرور على الواجبات وربط كل واجب بالكورس المناسب.
        foreach ($assignments as $assignmentData) {
            $course = $courses->firstWhere(
                'title',
                $assignmentData['course_title']
            );

            // تخطي الواجب إذا لم نعثر على الكورس المطلوب.
            if (!$course) {
                $this->command->warn(
                    "Course not found: {$assignmentData['course_title']}"
                );

                continue;
            }

            // إزالة عنوان الكورس لأنه ليس عموداً في جدول assignments.
            unset($assignmentData['course_title']);

            // إنشاء الواجب إن لم يكن موجوداً ضمن الكورس نفسه.
            $course->assignments()->firstOrCreate(
                ['title' => $assignmentData['title']],
                $assignmentData
            );
        }
    }
}
