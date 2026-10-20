<?php

namespace App\Providers;

use App\Contracts\CourseOwnershipChecker;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Services\DefaultCourseOwnershipChecker;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CourseOwnershipChecker::class,
            DefaultCourseOwnershipChecker::class
        );
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => User::class,
            'course' => Course::class,
            'lesson' => Lesson::class,
            'assignment' => Assignment::class,
        ]);
    }
}
