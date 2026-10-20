<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_student_can_comment_on_a_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::withoutGlobalScopes()->create([
            'teacher_id' => $teacher->id,
            'title' => 'Test Course',
            'level' => 'beginner',
            'status' => 'published',
            'is_free' => true,
        ]);

        $student->enrolledCourses()->attach($course->id);

        $response = $this->actingAs($student, 'sanctum')->postJson(
            "/api/courses/{$course->id}/comments",
            ['body' => 'تعليق تجريبي']
        );

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.body', 'تعليق تجريبي');

        $this->assertDatabaseHas('comments', [
            'user_id' => $student->id,
            'commentable_id' => $course->id,
            'commentable_type' => 'course',
            'body' => 'تعليق تجريبي',
        ]);
    }

    public function test_unenrolled_student_cannot_comment_on_a_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::withoutGlobalScopes()->create([
            'teacher_id' => $teacher->id,
            'title' => 'Test Course',
            'level' => 'beginner',
            'status' => 'published',
            'is_free' => true,
        ]);

        $response = $this->actingAs($student, 'sanctum')->postJson(
            "/api/courses/{$course->id}/comments",
            ['body' => 'تعليق غير مسموح']
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('comments', [
            'user_id' => $student->id,
            'commentable_id' => $course->id,
            'commentable_type' => 'course',
        ]);
    }

    public function test_student_can_comment_on_a_lesson_if_enrolled_in_its_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::withoutGlobalScopes()->create([
            'teacher_id' => $teacher->id,
            'title' => 'Test Course',
            'level' => 'beginner',
            'status' => 'published',
            'is_free' => true,
        ]);

        $lesson = $course->lessons()->create([
            'title' => 'Test Lesson',
            'sort_order' => 1,
        ]);

        $student->enrolledCourses()->attach($course->id);

        $response = $this->actingAs($student, 'sanctum')->postJson(
            "/api/lessons/{$lesson->id}/comments",
            ['body' => 'تعليق على الدرس']
        );

        $response->assertCreated();

        $this->assertDatabaseHas('comments', [
            'user_id' => $student->id,
            'commentable_id' => $lesson->id,
            'commentable_type' => 'lesson',
            'body' => 'تعليق على الدرس',
        ]);
    }

    public function test_student_can_comment_on_an_assignment_if_enrolled_in_its_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::withoutGlobalScopes()->create([
            'teacher_id' => $teacher->id,
            'title' => 'Test Course',
            'level' => 'beginner',
            'status' => 'published',
            'is_free' => true,
        ]);

        $assignment = $course->assignments()->create([
            'title' => 'Test Assignment',
        ]);

        $student->enrolledCourses()->attach($course->id);

        $response = $this->actingAs($student, 'sanctum')->postJson(
            "/api/assignments/{$assignment->id}/comments",
            ['body' => 'تعليق على الواجب']
        );

        $response->assertCreated();

        $this->assertDatabaseHas('comments', [
            'user_id' => $student->id,
            'commentable_id' => $assignment->id,
            'commentable_type' => 'assignment',
            'body' => 'تعليق على الواجب',
        ]);
    }

    public function test_guest_cannot_create_a_comment(): void
    {
        $response = $this->postJson('/api/courses/1/comments', [
            'body' => 'تعليق',
        ]);

        $response->assertUnauthorized();
    }

    public function test_comment_body_is_required(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::withoutGlobalScopes()->create([
            'teacher_id' => $teacher->id,
            'title' => 'Test Course',
            'level' => 'beginner',
            'status' => 'published',
            'is_free' => true,
        ]);

        $student->enrolledCourses()->attach($course->id);

        $response = $this->actingAs($student, 'sanctum')->postJson(
            "/api/courses/{$course->id}/comments",
            ['body' => '']
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['body']);
    }
    public function test_teacher_cannot_create_a_comment(): void
{
    $teacher = User::factory()->create(['role' => 'teacher']);

    $response = $this->actingAs($teacher, 'sanctum')->postJson(
        '/api/courses/1/comments',
        ['body' => 'تعليق من المدرس']
    );

    $response->assertForbidden();
}

public function test_student_gets_not_found_for_missing_course(): void
{
    $student = User::factory()->create(['role' => 'student']);

    $response = $this->actingAs($student, 'sanctum')->postJson(
        '/api/courses/99999/comments',
        ['body' => 'تعليق على كورس غير موجود']
    );

    $response->assertNotFound();
}
public function test_student_cannot_comment_on_an_unpublished_course(): void
{
    $student = User::factory()->create(['role' => 'student']);
    $teacher = User::factory()->create(['role' => 'teacher']);

    $course = Course::withoutGlobalScopes()->create([
        'teacher_id' => $teacher->id,
        'title' => 'Draft Course',
        'level' => 'beginner',
        'status' => 'draft',
        'is_free' => true,
    ]);

    $student->enrolledCourses()->attach($course->id);

    $response = $this->actingAs($student, 'sanctum')->postJson(
        "/api/courses/{$course->id}/comments",
        ['body' => 'تعليق على كورس غير منشور']
    );

    $response->assertForbidden();

    $this->assertDatabaseMissing('comments', [
        'user_id' => $student->id,
        'commentable_id' => $course->id,
        'commentable_type' => 'course',
    ]);
}
public function test_latest_comments_include_student_and_content_without_n_plus_one(): void
{
    $student = User::factory()->create([
        'role' => 'student',
        'name' => 'طالب تجريبي',
    ]);

    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $course = Course::withoutGlobalScopes()->create([
        'teacher_id' => $teacher->id,
        'title' => 'كورس تجريبي',
        'level' => 'beginner',
        'status' => 'published',
        'is_free' => true,
    ]);

    $lesson = $course->lessons()->create([
        'title' => 'درس تجريبي',
        'sort_order' => 1,
    ]);

    $assignment = $course->assignments()->create([
        'title' => 'واجب تجريبي',
    ]);

    $course->comments()->create([
        'user_id' => $student->id,
        'body' => 'تعليق على الكورس',
    ]);

    $lesson->comments()->create([
        'user_id' => $student->id,
        'body' => 'تعليق على الدرس',
    ]);

    $assignment->comments()->create([
        'user_id' => $student->id,
        'body' => 'تعليق على الواجب',
    ]);

    // تفعيل تسجيل الاستعلامات بعد تجهيز بيانات الاختبار.
    $connection = DB::connection();
    $connection->flushQueryLog();
    $connection->enableQueryLog();

    try {
        $response = $this->getJson('/api/comments/latest');

        $queries = collect($connection->getQueryLog())
            ->filter(
                fn (array $query): bool =>
                    preg_match('/^\s*select\b/i', $query['query']) === 1
            );

        $selectQueryCount = $queries->count();
        fwrite(STDOUT, "\nعدد استعلامات SELECT الفعلي: {$selectQueryCount}\n");
    } finally {
        $connection->disableQueryLog();
    }

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonFragment([
            'student' => 'طالب تجريبي',
            'content_name' => 'كورس تجريبي',
        ])
        ->assertJsonFragment([
            'student' => 'طالب تجريبي',
            'content_name' => 'درس تجريبي',
        ])
        ->assertJsonFragment([
            'student' => 'طالب تجريبي',
            'content_name' => 'واجب تجريبي',
        ]);

    // تعليق واحد أو عدة تعليقات يجب ألا ينتج عنها استعلام لكل تعليق.
    $this->assertLessThanOrEqual(
        5,
        $selectQueryCount,
        "عدد استعلامات SELECT المتوقع ألا يتجاوز 5، والفعلي: {$selectQueryCount}"
    );
}
}
