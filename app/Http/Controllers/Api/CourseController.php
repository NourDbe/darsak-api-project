<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Requests\DestroyCourseRequest;
use App\Models\Course;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    /**
     * عرض الدورات مع بيانات المدرّس والتصنيفات والصورة.
     */
    public function index(): JsonResponse
    {
        $courses = Course::query()
            ->with(['categories', 'image'])
            ->with('teacher:id,name')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الدورات بنجاح.',
            'data' => $courses,
        ]);
    }

    /**
     * إنشاء دورة جديدة للمدرّس المسجّل دخوله.
     */
    public function store(StoreCourseRequest $request): JsonResponse
    {
        $course = $request->user()->courses()->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الدورة بنجاح.',
            'data' => $course->load(['categories', 'image', 'teacher:id,name']),
        ], 201);
    }

    /**
     * عرض دورة محددة.
     */
    public function show(Course $course): JsonResponse
{
    $course->load([
        'categories',
        'image',
        'teacher:id,name',
        'lessons' => function ($query) {
            $query->with(['categories', 'image'])
                ->orderBy('sort_order')
                ->orderBy('id');
        },
        'assignments' => function ($query) {
            $query->with(['categories', 'image'])
                ->latest();
        },
    ]);

    return response()->json([
        'success' => true,
        'message' => 'تم جلب الدورة بنجاح.',
        'data' => $course,
    ]);
}

    /**
     * تعديل دورة يملكها المدرّس المسجّل دخوله.
     */
    public function update(
        UpdateCourseRequest $request,
        Course $course
    ): JsonResponse {
        $course->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل الدورة بنجاح.',
            'data' => $course->fresh()->load([
                'categories',
                'image',
                'teacher:id,name',
            ]),
        ]);
    }

    /**
     * حذف دورة يملكها المدرّس المسجّل دخوله.
     */
    public function destroy(
    DestroyCourseRequest $request,
    Course $course
): JsonResponse {
    $course->delete();

    return response()->json([
        'success' => true,
        'message' => 'تم حذف الدورة بنجاح.',
        'data' => null,
    ]);
}
}
