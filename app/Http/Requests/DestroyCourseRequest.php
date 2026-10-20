<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use App\Contracts\CourseOwnershipChecker;


class DestroyCourseRequest extends FormRequest
{
    public function authorize(): bool
{
    $user = $this->user();
    $course = $this->route('course');

    if ($user === null || ! $course instanceof Course) {
        return false;
    }

    return app(CourseOwnershipChecker::class)->owns(
        $user,
        $course
    );
}

    public function rules(): array
    {
        return [];
    }
}
