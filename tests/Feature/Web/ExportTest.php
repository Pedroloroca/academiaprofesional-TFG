<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->teacherUser = User::factory()->create();
    $this->teacherUser->assignRole('teacher');
    $this->teacher = Teacher::factory()->create(['user_id' => $this->teacherUser->id]);

    $this->studentUser = User::factory()->create();
    $this->studentUser->assignRole('student');
    $this->student = Student::factory()->create(['user_id' => $this->studentUser->id]);

    $this->course = Course::factory()->create(['teacher_id' => $this->teacher->id]);
});

test('admin can export all students', function () {
    $this->actingAs($this->admin)
        ->get(route('export.students'))
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
});

test('admin can export all courses', function () {
    $this->actingAs($this->admin)
        ->get(route('export.courses'))
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
});

test('teacher can export students of their own course', function () {
    Enrollment::factory()->create([
        'course_id' => $this->course->id,
        'student_id' => $this->student->id
    ]);

    $this->actingAs($this->teacherUser)
        ->get(route('export.course.students', $this->course))
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
});

test('teacher cannot export students of another teacher course', function () {
    $otherTeacherUser = User::factory()->create();
    $otherTeacherUser->assignRole('teacher');
    $otherTeacher = Teacher::factory()->create(['user_id' => $otherTeacherUser->id]);
    $otherCourse = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

    $this->actingAs($this->teacherUser)
        ->get(route('export.course.students', $otherCourse))
        ->assertStatus(403);
});

test('student can export their enrolled courses', function () {
    Enrollment::factory()->create([
        'course_id' => $this->course->id,
        'student_id' => $this->student->id
    ]);

    $this->actingAs($this->studentUser)
        ->get(route('export.my-courses'))
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
});

test('unauthorized user cannot export students', function () {
    $this->actingAs($this->studentUser)
        ->get(route('export.students'))
        ->assertStatus(403);
});
