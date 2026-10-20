<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Assignment;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * إضافة تعليق على كورس أو درس أو واجب.
     */
    public function store(
        StoreCommentRequest $request,
        string $type,
        int $id
    ): JsonResponse {
        // تحديد نوع المحتوى المطلوب التعليق عليه.
        $modelClass = match ($type) {
            'courses' => Course::class,
            'lessons' => Lesson::class,
            'assignments' => Assignment::class,
        };

        // جلب المحتوى المطلوب التعليق عليه.
        $content = $modelClass::query()->findOrFail($id);

        // إنشاء التعليق وربطه بالمحتوى الحالي.
        $comment = $content->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        // تحميل بيانات صاحب التعليق.
        $comment->load('user:id,name');

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة التعليق بنجاح.',
            'data' => $comment,
        ], 201);
    }

    /**
     * عرض آخر 30 تعليقاً مع الطالب والمحتوى المرتبط.
     */
    public function latest(): JsonResponse
    {
        // تحميل العلاقات مسبقاً لتجنّب استعلام لكل تعليق.
        $comments = Comment::query()
            ->with([
                'user:id,name',
                'commentable',
            ])
            ->latest('id')
            ->limit(30)
            ->get();

        // توحيد بيانات المحتوى لتسهيل استهلاكها من الواجهة.
        $data = $comments->map(function (Comment $comment): array {
            $content = $comment->commentable;

            return [
                'id' => $comment->id,
                'body' => $comment->body,
                'created_at' => $comment->created_at,
                'student' => $comment->user?->name,
                'content_type' => match (true) {
                    $content instanceof Course => 'course',
                    $content instanceof Lesson => 'lesson',
                    $content instanceof Assignment => 'assignment',
                    default => 'unknown',
                },
                'content_id' => $content?->id,
                'content_name' => $content?->title,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'تم جلب آخر التعليقات بنجاح.',
            'data' => $data,
        ]);
    }
}
