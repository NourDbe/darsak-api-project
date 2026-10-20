<?php

namespace App\Contracts;

use App\Models\Course;
use App\Models\User;

interface CourseOwnershipChecker
{
    public function owns(User $user, Course $course): bool;
}
