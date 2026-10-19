<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
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
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_at' => ['sometimes', 'nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الواجب مطلوب عند إرساله.',
            'title.string' => 'عنوان الواجب يجب أن يكون نصاً.',
            'title.max' => 'عنوان الواجب يجب ألا يتجاوز 255 حرفاً.',
            'description.string' => 'وصف الواجب يجب أن يكون نصاً.',
            'due_at.date' => 'موعد تسليم الواجب يجب أن يكون تاريخاً ووقتاً صالحين.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'عنوان الواجب',
            'description' => 'وصف الواجب',
            'due_at' => 'موعد التسليم',
        ];
    }
}
