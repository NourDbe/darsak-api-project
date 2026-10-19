<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_list_assignments_for_a_course(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        Assignment::factory()->count(2)->create([
            'course_id' => $course->id,
        ]);

        $response = $this->getJson(
            "/api/courses/{$course->id}/assignments"
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data.data');
    }

    public function test_anyone_can_view_an_assignment(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
        ]);

        $response = $this->getJson(
            "/api/assignments/{$assignment->id}"
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $assignment->id);
    }

    public function test_guest_cannot_create_an_assignment(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $response = $this->postJson(
            "/api/courses/{$course->id}/assignments",
            ['title' => 'واجب جديد']
        );

        $response->assertUnauthorized();
    }

    public function test_course_owner_can_create_an_assignment(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $response = $this->actingAs($teacher)->postJson(
            "/api/courses/{$course->id}/assignments",
            [
                'title' => 'الواجب الأول',
                'description' => 'حل استعلامات SQL',
                'due_at' => '2026-11-01 23:59:00',
            ]
        );

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'الواجب الأول');

        $this->assertDatabaseHas('assignments', [
            'course_id' => $course->id,
            'title' => 'الواجب الأول',
        ]);
    }

    public function test_teacher_cannot_create_assignment_in_another_teachers_course(): void
    {
        $owner = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherTeacher)->postJson(
            "/api/courses/{$course->id}/assignments",
            ['title' => 'واجب غير مسموح']
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('assignments', [
            'course_id' => $course->id,
            'title' => 'واجب غير مسموح',
        ]);
    }

    public function test_course_owner_can_update_an_assignment(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'title' => 'العنوان القديم',
        ]);

        $response = $this->actingAs($teacher)->patchJson(
            "/api/assignments/{$assignment->id}",
            ['title' => 'العنوان الجديد']
        );

        $response->assertOk()
            ->assertJsonPath('data.title', 'العنوان الجديد');

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'العنوان الجديد',
        ]);
    }

    public function test_teacher_cannot_update_another_teachers_assignment(): void
    {
        $owner = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $owner->id,
        ]);

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'title' => 'العنوان الأصلي',
        ]);

        $response = $this->actingAs($otherTeacher)->patchJson(
            "/api/assignments/{$assignment->id}",
            ['title' => 'تعديل غير مسموح']
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'العنوان الأصلي',
        ]);
    }

    public function test_course_owner_can_delete_an_assignment(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
        ]);

        $response = $this->actingAs($teacher)->deleteJson(
            "/api/assignments/{$assignment->id}"
        );

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('assignments', [
            'id' => $assignment->id,
        ]);
    }

    public function test_teacher_cannot_delete_another_teachers_assignment(): void
    {
        $owner = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $owner->id,
        ]);

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
        ]);

        $response = $this->actingAs($otherTeacher)->deleteJson(
            "/api/assignments/{$assignment->id}"
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
        ]);
    }

    public function test_partial_update_keeps_other_assignment_fields_unchanged(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'title' => 'العنوان القديم',
            'description' => 'وصف يجب أن يبقى كما هو',
            'due_at' => '2026-11-01 23:59:00',
        ]);

        $response = $this->actingAs($teacher)->patchJson(
            "/api/assignments/{$assignment->id}",
            ['title' => 'العنوان الجديد']
        );

        $response->assertOk();

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'العنوان الجديد',
            'description' => 'وصف يجب أن يبقى كما هو',
            'due_at' => '2026-11-01 23:59:00',
        ]);
    }

    public function test_assignment_requires_a_title(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $response = $this->actingAs($teacher)->postJson(
            "/api/courses/{$course->id}/assignments",
            ['description' => 'واجب بلا عنوان']
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->assertDatabaseMissing('assignments', [
            'course_id' => $course->id,
            'description' => 'واجب بلا عنوان',
        ]);
    }
    public function test_assignment_supports_polymorphic_comments_images_and_categories(): void
{
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $assignment = Assignment::factory()->create([
        'course_id' => $course->id,
    ]);

    $student = User::factory()->create([
        'role' => 'student',
    ]);

    // إضافة تعليق للواجب.
    $comment = $assignment->comments()->create([
        'user_id' => $student->id,
        'body' => 'هذا تعليق تجريبي على الواجب.',
    ]);

    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'commentable_type' => 'assignment',
        'commentable_id' => $assignment->id,
    ]);

    // إضافة صورة للواجب.
    $image = $assignment->image()->create([
        'path' => 'assignments/test-image.jpg',
    ]);

    $this->assertDatabaseHas('images', [
        'id' => $image->id,
        'imageable_type' => 'assignment',
        'imageable_id' => $assignment->id,
    ]);

    // إضافة تصنيف للواجب.
    $category = Category::create([
    'name' => 'تصنيف تجريبي',
    'slug' => 'test-assignment-category',
    ]);

$assignment->categories()->attach($category->id);

    $this->assertTrue(
        $assignment->categories()
            ->where('categories.id', $category->id)
            ->exists()
    );
}
}
