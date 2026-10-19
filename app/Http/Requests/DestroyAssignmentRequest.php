<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $assignment = $this->route('assignment');

        return $user !== null
            && $user->role === 'teacher'
            && $assignment !== null
            && $assignment->course !== null
            && (int) $assignment->course->teacher_id === (int) $user->id;
    }

    public function rules(): array
    {
        return [];
    }
}
