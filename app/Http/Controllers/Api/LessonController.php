<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;

class LessonController extends Controller
{
    /**
     * عرض دروس دورة محددة.
     */
    public function index(Course $course): JsonResponse
    {
        $lessons = $course->lessons()
            ->with(['categories', 'image'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب دروس الدورة بنجاح.',
            'data' => $lessons,
        ]);
    }

    /**
     * عرض تفاصيل درس محدد.
     */
    public function show(Lesson $lesson): JsonResponse
    {
        $lesson->load(['course', 'categories', 'image']);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب تفاصيل الدرس بنجاح.',
            'data' => $lesson,
        ]);
    }
    /**
 * إنشاء درس جديد ضمن دورة يملكها المدرّس.
 */
public function store(
    \App\Http\Requests\StoreLessonRequest $request,
    \App\Models\Course $course
): \Illuminate\Http\JsonResponse {
    $lesson = $course->lessons()->create($request->validated());

    $lesson->load(['course', 'categories', 'image']);

    return response()->json([
        'success' => true,
        'message' => 'تم إنشاء الدرس بنجاح.',
        'data' => $lesson,
    ], 201);
}
/**
 * تعديل درس موجود، مع دعم التعديل الجزئي.
 */
public function update(
    \App\Http\Requests\UpdateLessonRequest $request,
    \App\Models\Lesson $lesson
): \Illuminate\Http\JsonResponse {
    $lesson->update($request->validated());

    $lesson->load(['course', 'categories', 'image']);

    return response()->json([
        'success' => true,
        'message' => 'تم تحديث الدرس بنجاح.',
        'data' => $lesson,
    ]);
}
/**
 * حذف درس يملكه المدرّس الحالي.
 */
public function destroy(
    \App\Http\Requests\DestroyLessonRequest $request,
    \App\Models\Lesson $lesson
): \Illuminate\Http\JsonResponse {
    $lesson->delete();

    return response()->json([
        'success' => true,
        'message' => 'تم حذف الدرس بنجاح.',
        'data' => null,
    ]);
}
}
