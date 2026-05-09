<?php

use App\Livewire\LessonViewer;
use App\Livewire\PublicCatalog;
use App\Livewire\HomePage;
use Livewire\Livewire;
use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

test('lesson viewer acts correctly for teacher', function () {
    $user = User::factory()->create();
    $user->assignRole('teacher');
    $teacher = Teacher::factory()->create(['user_id' => $user->id]);
    $course = Course::factory()->create(['teacher_id' => $teacher->id, 'slug' => 'test-course']);
    $lesson = Lesson::factory()->create(['course_id' => $course->id, 'is_published' => true]);

    Livewire::actingAs($user)
        ->test(LessonViewer::class, ['slug' => 'test-course'])
        ->assertSee($course->title)
        ->assertSet('isTeacherOrAdmin', true)
        ->set('courseTitle', 'Updated Course Title')
        ->call('updateCourse')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'Updated Course Title']);

    Livewire::actingAs($user)
        ->test(LessonViewer::class, ['slug' => 'test-course'])
        ->set('new_lesson_title', 'New Lesson')
        ->set('new_lesson_content', 'New Content')
        ->call('addLesson')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('lessons', ['title' => 'New Lesson', 'course_id' => $course->id]);
});

test('student can complete lesson', function () {
    $user = User::factory()->create();
    $user->assignRole('student');
    $student = Student::factory()->create(['user_id' => $user->id]);
    $course = Course::factory()->create(['slug' => 'student-course']);
    $lesson = Lesson::factory()->create(['course_id' => $course->id, 'is_published' => true]);
    
    Enrollment::create(['student_id' => $student->id, 'course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now()]);

    Livewire::actingAs($user)
        ->test(LessonViewer::class, ['slug' => 'student-course'])
        ->call('completeLesson', $lesson->id)
        ->assertHasNoErrors()
        ->assertStatus(200);
});

test('homepage renders correctly', function () {
    Course::factory()->count(3)->create(['status' => 'published']);
    
    Livewire::test(HomePage::class)
        ->assertSee(__('Nuevos cursos disponibles'))
        ->assertStatus(200);
});

test('public catalog renders correctly', function () {
    Course::factory()->create(['status' => 'published', 'scope' => 'profesional', 'title' => 'Professional Course']);
    
    Livewire::test(PublicCatalog::class, ['scope' => 'profesional'])
        ->assertSee('Professional Course')
        ->assertStatus(200);
});

test('teacher directory renders correctly', function () {
    $user = User::factory()->create(['name' => 'Profesor de Prueba']);
    Teacher::factory()->create(['user_id' => $user->id]);

    Livewire::test(\App\Livewire\TeacherDirectory::class)
        ->assertSee('Profesor de Prueba')
        ->assertStatus(200);
});
