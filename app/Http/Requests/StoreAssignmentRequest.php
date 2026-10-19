<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $course = $this->route('course');

        return $user !== null
            && $user->role === 'teacher'
            && $course !== null
            && (int) $course->teacher_id === (int) $user->id;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الواجب مطلوب.',
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
