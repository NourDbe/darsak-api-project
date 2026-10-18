<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    /**
     * التحقق من صلاحية المدرّس لتعديل دورته فقط.
     */
    public function authorize(): bool
    {
        $course = $this->route('course');

        return $this->user() !== null
            && $this->user()->role === 'teacher'
            && $course !== null
            && (int) $course->teacher_id === (int) $this->user()->id;
    }

    /**
     * قواعد التحقق من البيانات المرسلة.
     * استخدام sometimes يسمح بإرسال الحقول المراد تعديلها فقط.
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'level' => [
                'sometimes',
                'required',
                Rule::in(['beginner', 'intermediate', 'advanced']),
            ],
            'status' => [
                'sometimes',
                'required',
                Rule::in(['draft', 'published']),
            ],
            'is_free' => ['sometimes', 'boolean'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * رسائل التحقق باللغة العربية.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الدورة مطلوب عند إرساله.',
            'title.string' => 'عنوان الدورة يجب أن يكون نصاً.',
            'title.max' => 'عنوان الدورة يجب ألا يتجاوز 255 حرفاً.',
            'description.string' => 'وصف الدورة يجب أن يكون نصاً.',
            'level.required' => 'مستوى الدورة مطلوب عند إرساله.',
            'level.in' => 'مستوى الدورة المحدد غير صالح.',
            'status.required' => 'حالة الدورة مطلوبة عند إرسالها.',
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
