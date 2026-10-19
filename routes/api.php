<?php

use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LessonController;
use Illuminate\Support\Facades\Route;

// عرض الدورات متاح بدون تسجيل دخول.
Route::get('/courses', [CourseController::class, 'index']);

Route::get('/courses/{course}', [CourseController::class, 'show'])
    ->whereNumber('course');

// عرض الدروس وتفاصيلها متاح بدون تسجيل دخول.
Route::get('/courses/{course}/lessons', [LessonController::class, 'index'])
    ->whereNumber('course');

Route::get('/lessons/{lesson}', [LessonController::class, 'show'])
    ->whereNumber('lesson');

// عمليات إنشاء وتعديل وحذف الدورات تتطلب تسجيل الدخول.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/courses', [CourseController::class, 'store']);

    Route::put('/courses/{course}', [CourseController::class, 'update'])
        ->whereNumber('course');

    Route::patch('/courses/{course}', [CourseController::class, 'update'])
        ->whereNumber('course');

    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])
        ->whereNumber('course');

    // إنشاء درس ضمن دورة.
    Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])
        ->whereNumber('course');

    // تعديل درس أو حذفه.
    Route::put('/lessons/{lesson}', [LessonController::class, 'update'])
        ->whereNumber('lesson');

    Route::patch('/lessons/{lesson}', [LessonController::class, 'update'])
        ->whereNumber('lesson');

    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])
        ->whereNumber('lesson');
});
