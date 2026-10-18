<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Assignment;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    Relation::enforceMorphMap
    ([
    'course' => Course::class,
    'lesson' => Lesson::class,
    'assignment' => Assignment::class,
    ]);
    }
}
