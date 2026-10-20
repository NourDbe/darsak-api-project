<?php

namespace App\Services;

use App\Contracts\CourseOwnershipChecker;
use App\Models\Course;
use App\Models\User;

class DefaultCourseOwnershipChecker implements CourseOwnershipChecker
{
    public function owns(User $user, Course $course): bool
    {
        return (int) $course->teacher_id === (int) $user->id
            && $user->role === 'teacher';
    }
}
