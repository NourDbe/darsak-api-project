<?php

namespace App\Http\Requests;

use App\Rules\ValidVideoUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLessonRequest extends FormRequest
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
            'video_url' => ['nullable', 'string', 'max:2048', new ValidVideoUrl()],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الدرس مطلوب.',
            'title.string' => 'عنوان الدرس يجب أن يكون نصاً.',
            'title.max' => 'عنوان الدرس يجب ألا يتجاوز 255 حرفاً.',
            'description.string' => 'وصف الدرس يجب أن يكون نصاً.',
            'video_url.url' => 'رابط الفيديو غير صالح.',
            'video_url.max' => 'رابط الفيديو طويل جداً.',
            'sort_order.integer' => 'ترتيب الدرس يجب أن يكون رقماً صحيحاً.',
            'sort_order.min' => 'ترتيب الدرس يجب ألا يكون سالباً.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'عنوان الدرس',
            'description' => 'وصف الدرس',
            'video_url' => 'رابط الفيديو',
            'sort_order' => 'ترتيب الدرس',
        ];
    }
}
