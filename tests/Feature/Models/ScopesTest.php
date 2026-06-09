<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Lesson;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

// ── Course Scopes ────────────────────────────────────────────────────────────

test('scopePublished filters courses correctly', function () {
    $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
    
    $publishedCourse = Course::factory()->create(['teacher_id' => $teacher->id, 'status' => 'published']);
    $draftCourse = Course::factory()->create(['teacher_id' => $teacher->id, 'status' => 'draft']);
    $archivedCourse = Course::factory()->create(['teacher_id' => $teacher->id, 'status' => 'archived']);

    $courses = Course::published()->get();

    expect($courses)->toHaveCount(1);
    expect($courses->first()->id)->toBe($publishedCourse->id);
});

test('scopeWithActiveEnrollments filters courses correctly', function () {
    $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
    
    $courseWithActive = Course::factory()->create(['teacher_id' => $teacher->id]);
    $courseWithoutActive = Course::factory()->create(['teacher_id' => $teacher->id]);
    
    $student1 = Student::factory()->create(['user_id' => User::factory()->create()->id]);
    $student2 = Student::factory()->create(['user_id' => User::factory()->create()->id]);

    // Active enrollment
    Enrollment::factory()->create([
        'course_id' => $courseWithActive->id,
        'student_id' => $student1->id,
        'status' => 'active'
    ]);

    // Completed enrollment
    Enrollment::factory()->create([
        'course_id' => $courseWithoutActive->id,
        'student_id' => $student2->id,
        'status' => 'completed'
    ]);

    $courses = Course::withActiveEnrollments()->get();

    expect($courses)->toHaveCount(1);
    expect($courses->first()->id)->toBe($courseWithActive->id);
});

// ── Student Scopes ───────────────────────────────────────────────────────────

test('scopeTopPerformers filters students correctly', function () {
    $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);
    
    $topStudent = Student::factory()->create(['user_id' => User::factory()->create()->id]);
    $averageStudent = Student::factory()->create(['user_id' => User::factory()->create()->id]);

    Enrollment::factory()->create([
        'course_id' => $course->id,
        'student_id' => $topStudent->id,
        'final_grade' => 9.5
    ]);

    Enrollment::factory()->create([
        'course_id' => $course->id,
        'student_id' => $averageStudent->id,
        'final_grade' => 7.0
    ]);

    $students = Student::topPerformers()->get();

    expect($students)->toHaveCount(1);
    expect($students->first()->id)->toBe($topStudent->id);
});

test('scopeNeedsReinforcement filters students correctly', function () {
    $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);
    
    $strugglingStudent = Student::factory()->create(['user_id' => User::factory()->create()->id]);
    $averageStudent = Student::factory()->create(['user_id' => User::factory()->create()->id]);
    $studentWithoutGrade = Student::factory()->create(['user_id' => User::factory()->create()->id]);

    Enrollment::factory()->create([
        'course_id' => $course->id,
        'student_id' => $strugglingStudent->id,
        'final_grade' => 4.5
    ]);

    Enrollment::factory()->create([
        'course_id' => $course->id,
        'student_id' => $averageStudent->id,
        'final_grade' => 6.0
    ]);

    Enrollment::factory()->create([
        'course_id' => $course->id,
        'student_id' => $studentWithoutGrade->id,
        'final_grade' => null
    ]);

    $students = Student::needsReinforcement()->get();

    expect($students)->toHaveCount(1);
    expect($students->first()->id)->toBe($strugglingStudent->id);
});

// ── Additional Scopes (Course, Teacher, Lesson) ─────────────────────────────

test('scopeClassroom filters courses correctly', function () {
    $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);

    $classroomCourse = Course::factory()->create(['teacher_id' => $teacher->id, 'is_classroom' => true]);
    $onlineCourse = Course::factory()->create(['teacher_id' => $teacher->id, 'is_classroom' => false]);

    $courses = Course::classroom()->get();

    expect($courses)->toHaveCount(1);
    expect($courses->first()->id)->toBe($classroomCourse->id);
});

test('scopeWithManyLessons filters courses correctly', function () {
    $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);

    $bigCourse = Course::factory()->create(['teacher_id' => $teacher->id]);
    $smallCourse = Course::factory()->create(['teacher_id' => $teacher->id]);

    // Add lessons to bigCourse (5 lessons)
    Lesson::factory()->count(5)->create(['course_id' => $bigCourse->id]);
    // Add lessons to smallCourse (2 lessons)
    Lesson::factory()->count(2)->create(['course_id' => $smallCourse->id]);

    $courses = Course::withManyLessons(5)->get();

    expect($courses)->toHaveCount(1);
    expect($courses->first()->id)->toBe($bigCourse->id);
});

test('scopeActive filters teachers correctly', function () {
    $activeTeacherUser = User::factory()->create();
    $activeTeacher = Teacher::factory()->create(['user_id' => $activeTeacherUser->id]);
    Course::factory()->create(['teacher_id' => $activeTeacher->id, 'status' => 'published']);

    $inactiveTeacherUser = User::factory()->create();
    $inactiveTeacher = Teacher::factory()->create(['user_id' => $inactiveTeacherUser->id]);
    Course::factory()->create(['teacher_id' => $inactiveTeacher->id, 'status' => 'draft']);

    $teachers = Teacher::active()->get();

    expect($teachers)->toHaveCount(1);
    expect($teachers->first()->id)->toBe($activeTeacher->id);
});

test('scopePublished filters lessons correctly', function () {
    $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $publishedLesson = Lesson::factory()->create(['course_id' => $course->id, 'is_published' => true]);
    $draftLesson = Lesson::factory()->create(['course_id' => $course->id, 'is_published' => false]);

    $lessons = Lesson::published()->get();

    expect($lessons)->toHaveCount(1);
    expect($lessons->first()->id)->toBe($publishedLesson->id);
});
