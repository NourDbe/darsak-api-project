<?php

use App\Http\Controllers\Api\CourseController;
use Illuminate\Support\Facades\Route;

// عرض الدورات متاح بدون تسجيل دخول حالياً.
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{course}', [CourseController::class, 'show'])
    ->whereNumber('course');

// عمليات الإنشاء والتعديل والحذف تتطلب تسجيل الدخول.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/courses', [CourseController::class, 'store']);

    Route::put('/courses/{course}', [CourseController::class, 'update'])
        ->whereNumber('course');

    Route::patch('/courses/{course}', [CourseController::class, 'update'])
        ->whereNumber('course');

    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])
        ->whereNumber('course');
});
