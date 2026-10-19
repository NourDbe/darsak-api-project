<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_returns_all_related_content_types(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
        ]);

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
        ]);

        $category = Category::create([
            'name' => 'تصنيف تجريبي',
            'slug' => 'test-category-content',
        ]);

        $category->courses()->attach($course->id);
        $category->lessons()->attach($lesson->id);
        $category->assignments()->attach($assignment->id);

        $response = $this->getJson(
            "/api/categories/{$category->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.category.id', $category->id)
            ->assertJsonPath('data.courses.0.id', $course->id)
            ->assertJsonPath('data.lessons.0.id', $lesson->id)
            ->assertJsonPath('data.assignments.0.id', $assignment->id);
    }
}
