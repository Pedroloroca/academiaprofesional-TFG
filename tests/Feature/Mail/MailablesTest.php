<?php

use App\Mail\EnrollmentConfirmation;
use App\Mail\LessonReminder;
use App\Mail\PaymentReceived;
use App\Mail\MonthlySummary;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

test('EnrollmentConfirmation mailable builds correctly', function () {
    $user = User::factory()->create();
    $student = Student::factory()->create(['user_id' => $user->id]);
    $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'status' => 'active'
    ]);

    $mailable = new EnrollmentConfirmation($enrollment);
    $mailable->assertSeeInHtml($course->title);
    $mailable->assertHasSubject('Confirmación de Matrícula');
});

test('LessonReminder mailable builds correctly', function () {
    $mailable = new LessonReminder('Curso Avanzado', 'Lección 1: Introducción');
    $mailable->assertSeeInHtml('Curso Avanzado');
    $mailable->assertSeeInHtml('Lección 1: Introducción');
    $mailable->assertHasSubject('Recordatorio de Lección');
});

test('PaymentReceived mailable builds correctly', function () {
    $user = User::factory()->create();
    $student = Student::factory()->create(['user_id' => $user->id]);
    $teacher = Teacher::factory()->create(['user_id' => User::factory()->create()->id]);
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'status' => 'active'
    ]);

    $payment = Payment::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'amount' => 50,
        'currency' => 'EUR',
        'status' => 'paid',
        'provider' => 'stripe',
        'transaction_id' => 'tx_test123',
        'paid_at' => now()
    ]);

    $mailable = new PaymentReceived($payment);
    $mailable->assertSeeInHtml($course->title);
    $mailable->assertSeeInHtml('tx_test123');
    $mailable->assertHasSubject('Confirmación de Pago Recibido');
});

test('MonthlySummary mailable builds correctly', function () {
    $mailable = new MonthlySummary('Pedro', 'Mayo de 2026', 4, 12);
    $mailable->assertSeeInHtml('Mayo de 2026');
    $mailable->assertSeeInHtml('Pedro');
    $mailable->assertHasSubject('Resumen Mensual de Actividad');
});

test('TeacherAssignment mailable builds correctly', function () {
    $teacherUser = User::factory()->create(['name' => 'Profesor Prueba']);
    $course = Course::factory()->create(['title' => 'Curso de Prueba']);

    $mailable = new \App\Mail\TeacherAssignment($teacherUser, $course);
    $mailable->assertSeeInHtml('Profesor Prueba');
    $mailable->assertSeeInHtml('Curso de Prueba');
    $mailable->assertHasSubject('Nueva Asignación de Curso');
});
