<?php

use Livewire\Livewire;
use App\Livewire\HomePage;
use App\Livewire\PublicCatalog;
use App\Livewire\EnrollmentForm;
use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

test('home page livewire component renders correctly', function () {
    Livewire::test(HomePage::class)
        ->assertSee(__('Proyectos 100% reales'));
});

test('public catalog livewire component renders correctly', function () {
    $teacher = Teacher::factory()->create();
    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
        'status' => 'published',
        'title' => 'Catalog Unique Title'
    ]);

    Livewire::test(PublicCatalog::class)
        ->assertSee('Catalog Unique Title');
});

test('enrollment form acts correctly', function () {
    $user = User::factory()->create();
    $user->assignRole('student');

    $teacher = Teacher::factory()->create();
    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
        'status' => 'published',
        'slug' => 'enroll-test-course-slug',
        'price' => 0,
    ]);

    Livewire::actingAs($user)
        ->test(EnrollmentForm::class, ['slug' => 'enroll-test-course-slug'])
        ->call('enroll')
        ->assertRedirect('/admin/courses');

    $this->assertDatabaseHas('enrollments', [
        'course_id' => $course->id
    ]);
});

test('lesson viewer livewire component actions work', function () {
    $user = User::where('email', 'admin@admin.com')->first();
    $teacher = Teacher::factory()->create();
    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
        'status' => 'published',
        'slug' => 'test-lesson-course'
    ]);
    $lesson = $course->lessons()->create([
        'title' => 'First Sample Lesson',
        'slug' => 'first-sample-lesson',
        'content' => 'Initial Lesson Content',
        'is_published' => true
    ]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\LessonViewer::class, ['slug' => 'test-lesson-course'])
        ->assertSee('First Sample Lesson')
        ->call('toggleEditMode')
        ->assertSet('editMode', false)
        ->set('new_lesson_title', 'New Created Lesson')
        ->set('new_lesson_content', 'Brand new content here')
        ->call('addLesson')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('lessons', [
        'title' => 'New Created Lesson'
    ]);

    // Test editing current lesson
    Livewire::actingAs($user)
        ->test(\App\Livewire\LessonViewer::class, ['slug' => 'test-lesson-course'])
        ->set('active_lesson_title', 'Updated Lesson Title')
        ->set('active_lesson_content', 'Updated lesson content is awesome')
        ->call('updateLesson')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('lessons', [
        'title' => 'Updated Lesson Title'
    ]);

    // Test deleting lesson
    Livewire::actingAs($user)
        ->test(\App\Livewire\LessonViewer::class, ['slug' => 'test-lesson-course'])
        ->call('deleteLesson', $lesson->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('lessons', [
        'id' => $lesson->id
    ]);
});

test('catalog selector livewire component renders correctly', function () {
    Livewire::test(\App\Livewire\CatalogSelector::class)
        ->assertSee(__('Escolar'))
        ->assertSee(__('Profesional'));
});

test('dashboard stats livewire component renders correctly', function () {
    Livewire::test(\App\Livewire\DashboardStats::class)
        ->assertSee('Total Cursos');
});

test('teacher directory livewire component renders correctly', function () {
    $teacher = Teacher::factory()->create();

    Livewire::test(\App\Livewire\TeacherDirectory::class)
        ->assertSee($teacher->user->name);
});


