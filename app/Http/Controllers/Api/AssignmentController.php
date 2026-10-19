<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Http\Requests\DestroyAssignmentRequest;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\JsonResponse;

class AssignmentController extends Controller
{
    public function index(Course $course): JsonResponse
    {
        $assignments = $course->assignments()
            ->with(['categories', 'image'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الواجبات بنجاح.',
            'data' => $assignments,
        ]);
    }

    public function show(Assignment $assignment): JsonResponse
    {
        $assignment->load(['course', 'categories', 'image']);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الواجب بنجاح.',
            'data' => $assignment,
        ]);
    }

    public function store(
        StoreAssignmentRequest $request,
        Course $course
    ): JsonResponse {
        $assignment = $course->assignments()->create(
            $request->validated()
        );

        $assignment->load(['course', 'categories', 'image']);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الواجب بنجاح.',
            'data' => $assignment,
        ], 201);
    }

    public function update(
        UpdateAssignmentRequest $request,
        Assignment $assignment
    ): JsonResponse {
        $assignment->update($request->validated());

        $assignment->load(['course', 'categories', 'image']);

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل الواجب بنجاح.',
            'data' => $assignment,
        ]);
    }

   public function destroy(
    DestroyAssignmentRequest $request,
    Assignment $assignment
): JsonResponse {
    $assignment->delete();

    return response()->json([
        'success' => true,
        'message' => 'تم حذف الواجب بنجاح.',
        'data' => null,
    ]);
}
}
