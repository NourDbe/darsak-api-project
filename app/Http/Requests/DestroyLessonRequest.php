<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $lesson = $this->route('lesson');

        return $user !== null
            && $user->role === 'teacher'
            && $lesson !== null
            && $lesson->course !== null
            && (int) $lesson->course->teacher_id === (int) $user->id;
    }

    public function rules(): array
    {
        return [];
    }
}
