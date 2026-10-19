<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'course' => Course::class,
            'lesson' => Lesson::class,
            'assignment' => Assignment::class,
        ]);
    }
}
