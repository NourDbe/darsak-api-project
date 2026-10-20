<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_list_lessons_for_a_course(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        Lesson::factory()->count(2)->create([
            'course_id' => $course->id,
        ]);

        $response = $this->getJson("/api/courses/{$course->id}/lessons");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data.data');
    }

    public function test_anyone_can_view_a_lesson(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
        ]);

        $response = $this->getJson("/api/lessons/{$lesson->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $lesson->id);
    }

    public function test_guest_cannot_create_a_lesson(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $response = $this->postJson(
            "/api/courses/{$course->id}/lessons",
            ['title' => 'درس جديد']
        );

        $response->assertUnauthorized();
    }

    public function test_course_owner_can_create_a_lesson(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $response = $this->actingAs($teacher)->postJson(
            "/api/courses/{$course->id}/lessons",
            [
                'title' => 'مقدمة Laravel',
                'description' => 'شرح أساسيات Laravel',
                'sort_order' => 1,
            ]
        );

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'مقدمة Laravel');

        $this->assertDatabaseHas('lessons', [
            'course_id' => $course->id,
            'title' => 'مقدمة Laravel',
        ]);
    }

    public function test_teacher_cannot_create_a_lesson_in_another_teachers_course(): void
    {
        $owner = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);

        $course = Course::factory()->create([
            'teacher_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherTeacher)->postJson(
            "/api/courses/{$course->id}/lessons",
            ['title' => 'درس غير مسموح']
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('lessons', [
            'course_id' => $course->id,
            'title' => 'درس غير مسموح',
        ]);
    }
    public function test_course_owner_can_update_a_lesson(): void
{
    $teacher = User::factory()->create(['role' => 'teacher']);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'العنوان القديم',
    ]);

    $response = $this->actingAs($teacher)->patchJson(
        "/api/lessons/{$lesson->id}",
        ['title' => 'العنوان الجديد']
    );

    $response->assertOk()
        ->assertJsonPath('data.title', 'العنوان الجديد');

    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'title' => 'العنوان الجديد',
    ]);
}

public function test_teacher_cannot_delete_another_teachers_lesson(): void
{
    $owner = User::factory()->create(['role' => 'teacher']);
    $otherTeacher = User::factory()->create(['role' => 'teacher']);

    $course = Course::factory()->create([
        'teacher_id' => $owner->id,
    ]);

    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
    ]);

    $response = $this->actingAs($otherTeacher)->deleteJson(
        "/api/lessons/{$lesson->id}"
    );

    $response->assertForbidden();

    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
    ]);
}
public function test_lesson_rejects_video_urls_from_unsupported_domains(): void
{
    $teacher = User::factory()->create(['role' => 'teacher']);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $response = $this->actingAs($teacher)->postJson(
        "/api/courses/{$course->id}/lessons",
        [
            'title' => 'درس تجريبي',
            'video_url' => 'https://example.com/video',
        ]
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['video_url']);

    $this->assertDatabaseMissing('lessons', [
        'course_id' => $course->id,
        'title' => 'درس تجريبي',
    ]);
}
public function test_partial_update_keeps_other_lesson_fields_unchanged(): void
{
    $teacher = User::factory()->create(['role' => 'teacher']);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'العنوان القديم',
        'description' => 'وصف يجب أن يبقى كما هو',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'sort_order' => 3,
    ]);

    $response = $this->actingAs($teacher)->patchJson(
        "/api/lessons/{$lesson->id}",
        ['title' => 'العنوان الجديد']
    );

    $response->assertOk();

    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'title' => 'العنوان الجديد',
        'description' => 'وصف يجب أن يبقى كما هو',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'sort_order' => 3,
    ]);
}
public function test_lesson_rejects_fake_youtube_domain(): void
{
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $response = $this->actingAs($teacher)->postJson(
        "/api/courses/{$course->id}/lessons",
        [
            'title' => 'درس تجريبي',
            'video_url' => 'https://youtube.com.example.com/video',
        ]
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['video_url']);

    $this->assertDatabaseMissing('lessons', [
        'course_id' => $course->id,
        'title' => 'درس تجريبي',
    ]);
}
public function test_lesson_accepts_a_valid_youtube_url(): void
{
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $videoUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

    $response = $this->actingAs($teacher)->postJson(
        "/api/courses/{$course->id}/lessons",
        [
            'title' => 'درس فيديو صالح',
            'video_url' => $videoUrl,
        ]
    );

    $response->assertCreated()
        ->assertJsonPath('data.video_url', $videoUrl);

    $this->assertDatabaseHas('lessons', [
        'course_id' => $course->id,
        'title' => 'درس فيديو صالح',
        'video_url' => $videoUrl,
    ]);
}
public function test_lesson_accepts_a_valid_vimeo_url(): void
{
    $teacher = User::factory()->create(['role' => 'teacher']);
    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $videoUrl = 'https://vimeo.com/123456789';

    $response = $this->actingAs($teacher)->postJson(
        "/api/courses/{$course->id}/lessons",
        [
            'title' => 'درس Vimeo',
            'video_url' => $videoUrl,
        ]
    );

    $response->assertCreated()
        ->assertJsonPath('data.video_url', $videoUrl);
}

public function test_lesson_accepts_a_short_youtube_url(): void
{
    $teacher = User::factory()->create(['role' => 'teacher']);
    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $videoUrl = 'https://youtu.be/dQw4w9WgXcQ';

    $response = $this->actingAs($teacher)->postJson(
        "/api/courses/{$course->id}/lessons",
        [
            'title' => 'درس برابط مختصر',
            'video_url' => $videoUrl,
        ]
    );

    $response->assertCreated()
        ->assertJsonPath('data.video_url', $videoUrl);
}

public function test_lesson_rejects_ftp_video_url(): void
{
    $teacher = User::factory()->create(['role' => 'teacher']);
    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $response = $this->actingAs($teacher)->postJson(
        "/api/courses/{$course->id}/lessons",
        [
            'title' => 'رابط غير آمن',
            'video_url' => 'ftp://youtube.com/video',
        ]
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['video_url']);
}

public function test_lesson_accepts_null_video_url(): void
{
    $teacher = User::factory()->create(['role' => 'teacher']);
    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $response = $this->actingAs($teacher)->postJson(
        "/api/courses/{$course->id}/lessons",
        [
            'title' => 'درس بلا فيديو',
            'video_url' => null,
        ]
    );

    $response->assertCreated()
        ->assertJsonPath('data.video_url', null);
}
}
