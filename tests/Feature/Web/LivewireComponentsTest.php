<?php

use App\Livewire\CourseManager;
use App\Livewire\StudentManager;
use Livewire\Livewire;
use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Student;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

test('course manager component renders and acts correctly', function () {
    $user = User::where('email', 'admin@admin.com')->first();
    $teacher = Teacher::factory()->create();

    Livewire::actingAs($user)
        ->test(CourseManager::class)
        ->assertSee(__('Cursos'))
        ->call('create')
        ->assertSet('isOpen', true)
        ->set('title', 'Test Livewire Course')
        ->set('description', 'Short description of the new course')
        ->set('price', 15.00)
        ->set('teacher_id', $teacher->id)
        ->set('status', 'draft')
        ->set('scope', 'profesional')
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('isOpen', false);

    $this->assertDatabaseHas('courses', [
        'title' => 'Test Livewire Course',
        'price' => 15.00
    ]);
});

test('student manager component renders and acts correctly', function () {
    $user = User::where('email', 'admin@admin.com')->first();

    Livewire::actingAs($user)
        ->test(StudentManager::class)
        ->assertSee(__('Gestión de Estudiantes'))
        ->call('create')
        ->assertSet('isOpen', true)
        ->set('name', 'Livewire Test Student')
        ->set('email', 'livewire_test_student@example.com')
        ->set('password', 'secret123')
        ->set('date_of_birth', '2000-01-01')
        ->set('address', '123 Fake Street')
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('isOpen', false);

    $this->assertDatabaseHas('users', [
        'name' => 'Livewire Test Student',
        'email' => 'livewire_test_student@example.com'
    ]);
});

test('course manager component edit, delete, restore actions work', function () {
    $user = User::where('email', 'admin@admin.com')->first();
    $teacher = Teacher::factory()->create();
    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
        'title' => 'Initial Title For Livewire edit'
    ]);

    Livewire::actingAs($user)
        ->test(CourseManager::class)
        ->call('edit', $course->id)
        ->assertSet('course_id', $course->id)
        ->assertSet('title', 'Initial Title For Livewire edit')
        ->set('title', 'Updated Title For Livewire edit')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => 'Updated Title For Livewire edit'
    ]);

    Livewire::actingAs($user)
        ->test(CourseManager::class)
        ->call('delete', $course->id)
        ->assertHasNoErrors();

    $this->assertSoftDeleted('courses', [
        'id' => $course->id
    ]);

    Livewire::actingAs($user)
        ->test(CourseManager::class)
        ->call('restore', $course->id)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'deleted_at' => null
    ]);

    Livewire::actingAs($user)
        ->test(CourseManager::class)
        ->call('delete', $course->id)
        ->call('forceDelete', $course->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('courses', [
        'id' => $course->id
    ]);
});

test('student manager component edit and delete actions work', function () {
    $user = User::where('email', 'admin@admin.com')->first();
    $studentUser = User::factory()->create();
    $student = Student::factory()->create(['user_id' => $studentUser->id]);

    Livewire::actingAs($user)
        ->test(StudentManager::class)
        ->call('edit', $student->id)
        ->assertSet('student_id', $student->id)
        ->set('name', 'Updated Student Name For Livewire')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $studentUser->id,
        'name' => 'Updated Student Name For Livewire'
    ]);

    Livewire::actingAs($user)
        ->test(StudentManager::class)
        ->call('delete', $student->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('students', [
        'id' => $student->id
    ]);
});

test('course manager validates required fields', function () {
    $user = User::where('email', 'admin@admin.com')->first();

    Livewire::actingAs($user)
        ->test(CourseManager::class)
        ->call('store')
        ->assertHasErrors(['title', 'description', 'price', 'teacher_id']);
});

