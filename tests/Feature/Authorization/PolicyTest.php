<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Teacher;
use App\Models\User;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

// ── COURSE POLICY ─────────────────────────────────────────────────────────────

describe('CoursePolicy', function () {

    test('published courses are visible to guests', function () {
        $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        $course  = Course::factory()->create(['teacher_id' => $teacher->id, 'status' => 'published']);

        expect((new \App\Policies\CoursePolicy())->view(null, $course))->toBeTrue();
    });

    test('draft courses are not visible to guests', function () {
        $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        $course  = Course::factory()->create(['teacher_id' => $teacher->id, 'status' => 'draft']);

        expect((new \App\Policies\CoursePolicy())->view(null, $course))->toBeFalse();
    });

    test('admin can view draft courses', function () {
        $admin   = User::where('email', 'admin@admin.com')->first();
        $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        $course  = Course::factory()->create(['teacher_id' => $teacher->id, 'status' => 'draft']);

        expect((new \App\Policies\CoursePolicy())->view($admin, $course))->toBeTrue();
    });

    test('admin can create courses', function () {
        $admin = User::where('email', 'admin@admin.com')->first();

        expect((new \App\Policies\CoursePolicy())->create($admin))->toBeTrue();
    });

    test('student cannot create courses', function () {
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');

        expect((new \App\Policies\CoursePolicy())->create($studentUser))->toBeFalse();
    });

    test('teacher can update their own course', function () {
        $teacherUser = User::factory()->create();
        $teacherUser->assignRole('teacher');
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $course  = Course::factory()->create(['teacher_id' => $teacher->id, 'status' => 'draft']);

        expect((new \App\Policies\CoursePolicy())->update($teacherUser, $course))->toBeTrue();
    });

    test('teacher cannot update another teacher course', function () {
        $teacherUser  = User::factory()->create();
        $teacherUser->assignRole('teacher');
        $teacher      = Teacher::factory()->create(['user_id' => $teacherUser->id]);

        $otherTeacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        $course       = Course::factory()->create(['teacher_id' => $otherTeacher->id, 'status' => 'draft']);

        expect((new \App\Policies\CoursePolicy())->update($teacherUser, $course))->toBeFalse();
    });

    test('update policy does not crash if teacher has no profile', function () {
        $teacherUser = User::factory()->create();
        $teacherUser->assignRole('teacher');
        // No Teacher profile created intentionally
        $otherTeacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
        $course       = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

        // Should return false, not throw an exception
        expect((new \App\Policies\CoursePolicy())->update($teacherUser, $course))->toBeFalse();
    });
});

// ── ENROLLMENT POLICY ─────────────────────────────────────────────────────────

describe('EnrollmentPolicy', function () {

    test('admin can view any enrollments', function () {
        $admin = User::where('email', 'admin@admin.com')->first();

        expect((new \App\Policies\EnrollmentPolicy())->viewAny($admin))->toBeTrue();
    });

    test('student cannot view all enrollments list', function () {
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');

        expect((new \App\Policies\EnrollmentPolicy())->viewAny($studentUser))->toBeFalse();
    });

    test('admin can create, update and delete enrollments', function () {
        $admin = User::where('email', 'admin@admin.com')->first();
        $enrollment = Enrollment::factory()->create();

        expect((new \App\Policies\EnrollmentPolicy())->create($admin))->toBeTrue();
        expect((new \App\Policies\EnrollmentPolicy())->update($admin, $enrollment))->toBeTrue();
        expect((new \App\Policies\EnrollmentPolicy())->delete($admin, $enrollment))->toBeTrue();
    });

    test('student cannot create, update, delete, restore enrollments', function () {
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');
        $enrollment = Enrollment::factory()->create();

        expect((new \App\Policies\EnrollmentPolicy())->create($studentUser))->toBeFalse();
        expect((new \App\Policies\EnrollmentPolicy())->update($studentUser, $enrollment))->toBeFalse();
        expect((new \App\Policies\EnrollmentPolicy())->delete($studentUser, $enrollment))->toBeFalse();
        expect((new \App\Policies\EnrollmentPolicy())->restore($studentUser, $enrollment))->toBeFalse();
        expect((new \App\Policies\EnrollmentPolicy())->forceDelete($studentUser, $enrollment))->toBeFalse();
    });

    test('admin can restore and force delete enrollments', function () {
        $admin = User::where('email', 'admin@admin.com')->first();
        $enrollment = Enrollment::factory()->create();

        expect((new \App\Policies\EnrollmentPolicy())->restore($admin, $enrollment))->toBeTrue();
        expect((new \App\Policies\EnrollmentPolicy())->forceDelete($admin, $enrollment))->toBeTrue();
    });
});

// ── LESSON POLICY ─────────────────────────────────────────────────────────────

describe('LessonPolicy', function () {
    test('admin can view, create, update and delete lessons', function () {
        $admin = User::where('email', 'admin@admin.com')->first();
        $lesson = \App\Models\Lesson::factory()->create();

        expect((new \App\Policies\LessonPolicy())->viewAny($admin))->toBeTrue();
        expect((new \App\Policies\LessonPolicy())->view($admin, $lesson))->toBeTrue();
        expect((new \App\Policies\LessonPolicy())->create($admin))->toBeTrue();
        expect((new \App\Policies\LessonPolicy())->update($admin, $lesson))->toBeTrue();
        expect((new \App\Policies\LessonPolicy())->delete($admin, $lesson))->toBeTrue();
    });

    test('teacher can view, update and delete their own lesson', function () {
        $teacherUser = User::factory()->create();
        $teacherUser->assignRole('teacher');
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $lesson = \App\Models\Lesson::factory()->create(['course_id' => $course->id]);

        expect((new \App\Policies\LessonPolicy())->view($teacherUser, $lesson))->toBeTrue();
        expect((new \App\Policies\LessonPolicy())->update($teacherUser, $lesson))->toBeTrue();
        expect((new \App\Policies\LessonPolicy())->delete($teacherUser, $lesson))->toBeTrue();
    });

    test('teacher cannot update or delete other teacher lesson', function () {
        $teacherUser = User::factory()->create();
        $teacherUser->assignRole('teacher');
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        
        $otherTeacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);
        $lesson = \App\Models\Lesson::factory()->create(['course_id' => $course->id]);

        expect((new \App\Policies\LessonPolicy())->view($teacherUser, $lesson))->toBeFalse();
        expect((new \App\Policies\LessonPolicy())->update($teacherUser, $lesson))->toBeFalse();
        expect((new \App\Policies\LessonPolicy())->delete($teacherUser, $lesson))->toBeFalse();
    });

    test('student can view lesson if enrolled in the course', function () {
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');
        $student = \App\Models\Student::factory()->create(['user_id' => $studentUser->id]);

        $course = Course::factory()->create();
        $lesson = \App\Models\Lesson::factory()->create(['course_id' => $course->id]);

        // Enroll student in course
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        expect((new \App\Policies\LessonPolicy())->view($studentUser, $lesson))->toBeTrue();
    });

    test('student cannot view lesson if not enrolled', function () {
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');
        $student = \App\Models\Student::factory()->create(['user_id' => $studentUser->id]);

        $course = Course::factory()->create();
        $lesson = \App\Models\Lesson::factory()->create(['course_id' => $course->id]);

        expect((new \App\Policies\LessonPolicy())->view($studentUser, $lesson))->toBeFalse();
    });
});

// ── STUDENT POLICY ────────────────────────────────────────────────────────────

describe('StudentPolicy', function () {
    test('admin and teacher can viewAny students, student cannot', function () {
        $admin = User::where('email', 'admin@admin.com')->first();
        
        $teacherUser = User::factory()->create();
        $teacherUser->assignRole('teacher');

        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');

        expect((new \App\Policies\StudentPolicy())->viewAny($admin))->toBeTrue();
        expect((new \App\Policies\StudentPolicy())->viewAny($teacherUser))->toBeTrue();
        expect((new \App\Policies\StudentPolicy())->viewAny($studentUser))->toBeFalse();
    });

    test('student can view and update their own profile', function () {
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');
        $student = \App\Models\Student::factory()->create(['user_id' => $studentUser->id]);

        expect((new \App\Policies\StudentPolicy())->view($studentUser, $student))->toBeTrue();
        expect((new \App\Policies\StudentPolicy())->update($studentUser, $student))->toBeTrue();
    });

    test('student cannot view or update other student profile', function () {
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');
        $student = \App\Models\Student::factory()->create(['user_id' => $studentUser->id]);

        $otherStudentUser = User::factory()->create();
        $otherStudentUser->assignRole('student');
        $otherStudent = \App\Models\Student::factory()->create(['user_id' => $otherStudentUser->id]);

        expect((new \App\Policies\StudentPolicy())->view($studentUser, $otherStudent))->toBeFalse();
        expect((new \App\Policies\StudentPolicy())->update($studentUser, $otherStudent))->toBeFalse();
    });
});

// ── TEACHER POLICY ────────────────────────────────────────────────────────────

describe('TeacherPolicy', function () {
    test('anyone can view teachers list or teacher details', function () {
        $teacher = Teacher::factory()->create();

        expect((new \App\Policies\TeacherPolicy())->viewAny(null))->toBeTrue();
        expect((new \App\Policies\TeacherPolicy())->view(null, $teacher))->toBeTrue();
    });

    test('teacher can update their own profile, but not other teacher profile', function () {
        $teacherUser = User::factory()->create();
        $teacherUser->assignRole('teacher');
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);

        $otherTeacherUser = User::factory()->create();
        $otherTeacherUser->assignRole('teacher');
        $otherTeacher = Teacher::factory()->create(['user_id' => $otherTeacherUser->id]);

        expect((new \App\Policies\TeacherPolicy())->update($teacherUser, $teacher))->toBeTrue();
        expect((new \App\Policies\TeacherPolicy())->update($teacherUser, $otherTeacher))->toBeFalse();
    });

    test('admin can create, update and delete teacher profiles', function () {
        $admin = User::where('email', 'admin@admin.com')->first();
        $teacher = Teacher::factory()->create();

        expect((new \App\Policies\TeacherPolicy())->create($admin))->toBeTrue();
        expect((new \App\Policies\TeacherPolicy())->update($admin, $teacher))->toBeTrue();
        expect((new \App\Policies\TeacherPolicy())->delete($admin, $teacher))->toBeTrue();
    });
});

