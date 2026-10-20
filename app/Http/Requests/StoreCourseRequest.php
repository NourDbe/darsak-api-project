<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\CourseLevel;

class StoreCourseRequest extends FormRequest
{
    /**
     * التحقق من صلاحية المستخدم لإنشاء دورة.
     */
    public function authorize(): bool
    {
        return $this->user() !== null
            && $this->user()->role === 'teacher';
    }

    /**
     * قواعد التحقق من بيانات الدورة.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => [
            'required',
                Rule::enum(CourseLevel::class),
],
            'status' => [
                'sometimes',
                Rule::in(['draft', 'published']),
            ],
            'is_free' => ['sometimes', 'boolean'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * رسائل التحقق باللغة العربية.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الدورة مطلوب.',
            'title.string' => 'عنوان الدورة يجب أن يكون نصاً.',
            'title.max' => 'عنوان الدورة يجب ألا يتجاوز 255 حرفاً.',
            'description.string' => 'وصف الدورة يجب أن يكون نصاً.',
            'level.required' => 'مستوى الدورة مطلوب.',
            'level.in' => 'مستوى الدورة المحدد غير صالح.',
            'status.in' => 'حالة الدورة المحددة غير صالحة.',
            'is_free.boolean' => 'حقل مجانية الدورة يجب أن يكون صحيحاً أو خطأ.',
            'price.numeric' => 'سعر الدورة يجب أن يكون رقماً.',
            'price.min' => 'سعر الدورة لا يمكن أن يكون سالباً.',
        ];
    }

    /**
     * أسماء الحقول باللغة العربية.
     */
    public function attributes(): array
    {
        return [
            'title' => 'عنوان الدورة',
            'description' => 'وصف الدورة',
            'level' => 'مستوى الدورة',
            'status' => 'حالة الدورة',
            'is_free' => 'مجانية الدورة',
            'price' => 'سعر الدورة',
        ];
    }
}
