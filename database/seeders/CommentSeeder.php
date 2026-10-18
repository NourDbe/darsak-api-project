<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * إضافة تعليقات تجريبية إلى أنواع مختلفة من المحتوى.
     */
    public function run(): void
    {
        // جلب طالب تجريبي؛ لأن الطالب هو صاحب التعليق.
        $student = User::where('role', 'student')->first();

        // التوقف إذا لم يوجد طالب.
        if (!$student) {
            $this->command->warn(
                'No student found. Seed users before comments.'
            );

            return;
        }

        // تحديد المحتوى الذي سنضيف إليه التعليقات.
        $targets = [
            [
                'model' => Course::class,
                'title' => 'مقدمة في البرمجة',
                'body' => 'شرح جميل وسهل الفهم.',
            ],
            [
                'model' => Lesson::class,
                'title' => 'ما هي البرمجة؟',
                'body' => 'أصبح مفهوم البرمجة أوضح بالنسبة لي.',
            ],
            [
                'model' => Assignment::class,
                'title' => 'الواجب الأول: أساسيات البرمجة',
                'body' => 'الواجب مفيد لتطبيق المعلومات.',
            ],
        ];

        // المرور على المحتوى وإضافة تعليق لكل عنصر موجود.
        foreach ($targets as $target) {

            // البحث عن الكورس أو الدرس أو الواجب بحسب نوع الموديل.
            $modelClass = $target['model'];

            $content = $modelClass::where(
                'title',
                $target['title']
            )->first();

            // تخطي العنصر إذا لم نجده في قاعدة البيانات.
            if (!$content) {
                $this->command->warn(
                    "Content not found: {$target['title']}"
                );

                continue;
            }

            // إنشاء التعليق عبر العلاقة polymorphic.
            // استخدام firstOrCreate يمنع تكرار التعليق نفسه عند إعادة التشغيل.
            $content->comments()->firstOrCreate(
                [
                    'user_id' => $student->id,
                    'body' => $target['body'],
                ]
            );
        }
    }
}
