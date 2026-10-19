<?php

use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

// عرض الدورات متاح بدون تسجيل دخول.
Route::get('/courses', [CourseController::class, 'index']);

Route::get('/courses/{course}', [CourseController::class, 'show'])
    ->whereNumber('course');

// عرض الدروس متاح بدون تسجيل دخول.
Route::get('/courses/{course}/lessons', [LessonController::class, 'index'])
    ->whereNumber('course');

Route::get('/lessons/{lesson}', [LessonController::class, 'show'])
    ->whereNumber('lesson');

// عرض الواجبات متاح بدون تسجيل دخول.
Route::get('/courses/{course}/assignments', [AssignmentController::class, 'index'])
    ->whereNumber('course');

Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])
    ->whereNumber('assignment');
// عرض كل المحتوى المرتبط بتصنيف.
Route::get('/categories/{category}', [
    CategoryController::class,
    'show',
])->whereNumber('category');

// العمليات التي تتطلب تسجيل الدخول.
Route::middleware('auth:sanctum')->group(function () {
    // الدورات.
    Route::post('/courses', [CourseController::class, 'store']);

    Route::put('/courses/{course}', [CourseController::class, 'update'])
        ->whereNumber('course');

    Route::patch('/courses/{course}', [CourseController::class, 'update'])
        ->whereNumber('course');

    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])
        ->whereNumber('course');

    // الدروس.
    Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])
        ->whereNumber('course');

    Route::put('/lessons/{lesson}', [LessonController::class, 'update'])
        ->whereNumber('lesson');

    Route::patch('/lessons/{lesson}', [LessonController::class, 'update'])
        ->whereNumber('lesson');

    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])
        ->whereNumber('lesson');

    // الواجبات.
    Route::post('/courses/{course}/assignments', [AssignmentController::class, 'store'])
        ->whereNumber('course');

    Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])
        ->whereNumber('assignment');

    Route::patch('/assignments/{assignment}', [AssignmentController::class, 'update'])
        ->whereNumber('assignment');

    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])
        ->whereNumber('assignment');
});
