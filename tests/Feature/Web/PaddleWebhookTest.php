<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Listeners\HandlePaddleTransactionCompleted;
use Laravel\Paddle\Events\TransactionCompleted;
use Laravel\Paddle\Transaction;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

test('HandlePaddleTransactionCompleted activates enrollment and creates payment', function () {
    $user = User::factory()->create();
    $student = Student::factory()->create(['user_id' => $user->id]);
    $course = Course::factory()->create(['price' => 50.00]);

    // Simular el payload de Paddle
    $payload = [
        'data' => [
            'custom_data' => [
                'course_id' => $course->id,
                'student_id' => $student->id,
            ]
        ]
    ];

    // Simular el objeto transacción
    $transaction = new Transaction([
        'paddle_id' => 'ct_12345',
        'total' => '50.00',
        'currency' => 'USD'
    ]);

    $event = new TransactionCompleted($user, $transaction, $payload);

    $listener = new HandlePaddleTransactionCompleted();
    $listener->handle($event);

    // Verificar que la matrícula está activa
    $this->assertDatabaseHas('enrollments', [
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active'
    ]);

    // Verificar que se creó el pago
    $this->assertDatabaseHas('payments', [
        'student_id' => $student->id,
        'course_id' => $course->id,
        'amount' => 50.00,
        'transaction_id' => 'ct_12345',
        'status' => 'completed'
    ]);
});

test('HandlePaddleTransactionCompleted logs warning if custom_data is missing', function () {
    Log::shouldReceive('warning')->once();

    $user = User::factory()->create();
    $transaction = new Transaction(['paddle_id' => 'ct_empty']);
    $payload = ['data' => []];
    
    $event = new TransactionCompleted($user, $transaction, $payload);

    $listener = new HandlePaddleTransactionCompleted();
    $listener->handle($event);
});
