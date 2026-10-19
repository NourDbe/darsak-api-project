<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * التأكد من إمكانية عرض قائمة الدورات.
     */
    public function test_courses_can_be_listed(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        Course::factory()->count(2)->create([
            'teacher_id' => $teacher->id,
        ]);

        $response = $this->getJson('/api/courses');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    /**
     * التأكد من إمكانية عرض دورة محددة.
     */
    public function test_existing_course_can_be_shown(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $response = $this->getJson("/api/courses/{$course->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $course->id);
    }

    /**
     * منع المستخدم غير المسجل من إنشاء دورة.
     */
    public function test_guest_cannot_create_course(): void
    {
        $response = $this->postJson('/api/courses', [
            'title' => 'دورة تجريبية',
            'level' => 'beginner',
        ]);

        $response->assertUnauthorized();
    }

    /**
     * رفض بيانات إنشاء الدورة غير الصالحة.
     */
    public function test_course_creation_requires_a_title(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $response = $this->actingAs($teacher, 'sanctum')
            ->postJson('/api/courses', [
                'level' => 'beginner',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }
    /**
 * التأكد من أن المدرّس لا يستطيع تعديل دورة لا يملكها.
 */
public function test_teacher_cannot_update_another_teachers_course(): void
{
    $owner = User::factory()->create([
        'role' => 'teacher',
    ]);

    $otherTeacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $course = Course::factory()->create([
        'teacher_id' => $owner->id,
    ]);

    $response = $this->actingAs($otherTeacher, 'sanctum')
        ->patchJson("/api/courses/{$course->id}", [
            'title' => 'عنوان غير مسموح',
        ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => $course->title,
    ]);
}
/**
 * التأكد من أن المدرّس لا يستطيع حذف دورة لا يملكها.
 */
public function test_teacher_cannot_delete_another_teachers_course(): void
{
    $owner = User::factory()->create([
        'role' => 'teacher',
    ]);

    $otherTeacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $course = Course::factory()->create([
        'teacher_id' => $owner->id,
    ]);

    $response = $this->actingAs($otherTeacher, 'sanctum')
        ->deleteJson("/api/courses/{$course->id}");

    $response->assertForbidden();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
    ]);
}
/**
 * التأكد من أن المدرّس يستطيع حذف دورته.
 */
public function test_teacher_can_delete_own_course(): void
{
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $response = $this->actingAs($teacher, 'sanctum')
        ->deleteJson("/api/courses/{$course->id}");

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data', null);

    $this->assertDatabaseMissing('courses', [
        'id' => $course->id,
    ]);
}
/**
 * التأكد من أن الطالب لا يستطيع حذف دورة.
 */
public function test_student_cannot_delete_course(): void
{
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $student = User::factory()->create([
        'role' => 'student',
    ]);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $response = $this->actingAs($student, 'sanctum')
        ->deleteJson("/api/courses/{$course->id}");

    $response->assertForbidden();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
    ]);
}
/**
 * التأكد من أن الزائر لا يستطيع حذف دورة.
 */
public function test_guest_cannot_delete_course(): void
{
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $response = $this->deleteJson("/api/courses/{$course->id}");

    $response->assertUnauthorized();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
    ]);
}
}
