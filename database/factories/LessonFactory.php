<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
