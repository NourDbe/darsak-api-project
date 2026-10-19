<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;

class DestroyCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');

        return $this->user() !== null
            && $this->user()->role === 'teacher'
            && $course instanceof Course
            && $course->teacher_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [];
    }
}
