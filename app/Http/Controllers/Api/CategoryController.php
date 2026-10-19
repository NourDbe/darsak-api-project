<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function show(Category $category): JsonResponse
    {
        $category->load([
            'courses.teacher:id,name',
            'courses.image',
            'lessons.course',
            'lessons.image',
            'assignments.course',
            'assignments.image',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب محتويات التصنيف بنجاح.',
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'courses' => $category->courses,
                'lessons' => $category->lessons,
                'assignments' => $category->assignments,
            ],
        ]);
    }
}
