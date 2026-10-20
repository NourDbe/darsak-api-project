<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null || $user->role !== 'student') {
            return false;
        }

        $type = $this->route('type');
        $id = $this->route('id');

        $modelClass = match ($type) {
            'courses' => Course::class,
            'lessons' => Lesson::class,
            'assignments' => Assignment::class,
            default => null,
        };

        if ($modelClass === null || ! is_numeric($id)) {
            return false;
        }

        $content = $modelClass === Course::class
        ? Course::withoutGlobalScopes()->find($id)
        : $modelClass::query()->find($id);

        // المحتوى غير الموجود يُعالج في مرحلة لاحقة لإرجاع 404.
        if ($content === null) {
            return true;
        }

        $course = match (true) {
            $content instanceof Course => $content,
            $content instanceof Lesson,
            $content instanceof Assignment => $content->course,
            default => null,
        };

        if ($course === null || $course->status !== 'published') {
            return false;
        }

        return $user->enrolledCourses()
            ->where('courses.id', $course->id)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'نص التعليق مطلوب.',
            'body.string' => 'نص التعليق يجب أن يكون نصاً صالحاً.',
            'body.max' => 'التعليق يجب ألا يتجاوز 5000 حرف.',
        ];
    }

    public function attributes(): array
    {
        return [
            'body' => 'نص التعليق',
        ];
    }
}
