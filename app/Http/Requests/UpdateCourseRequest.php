<?php

namespace App\Http\Requests;

use App\Contracts\CourseOwnershipChecker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\CourseLevel;

class UpdateCourseRequest extends FormRequest
{
    /**
     * التحقق من صلاحية المدرّس لتعديل دورته فقط.
     */
    public function authorize(): bool
{
    $user = $this->user();
    $course = $this->route('course');

    if ($user === null || $course === null) {
        return false;
    }

    return app(CourseOwnershipChecker::class)->owns(
        $user,
        $course
    );
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
                Rule::enum(CourseLevel::class),
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
