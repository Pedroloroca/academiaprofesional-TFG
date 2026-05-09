<?php

namespace App\Listeners;

use Laravel\Paddle\Events\TransactionCompleted;
use App\Models\Enrollment;
use App\Events\StudentEnrolled;
use App\Events\PaymentReceived;
use App\Jobs\UpdateCourseStats;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class HandlePaddleTransactionCompleted
{
    public function handle(TransactionCompleted $event)
    {
        $payload = $event->payload;
        $transaction = $event->transaction;
        
        $customData = $payload['data']['custom_data'] ?? null;
        
        if ($customData && isset($customData['course_id']) && isset($customData['student_id'])) {
            $courseId = $customData['course_id'];
            $studentId = $customData['student_id'];
            
            // Activate Enrollment
            $enrollment = Enrollment::updateOrCreate(
                ['student_id' => $studentId, 'course_id' => $courseId],
                ['status' => 'active', 'enrolled_at' => now()]
            );
            
            // Create Payment Record
            $payment = Payment::create([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'enrollment_id' => $enrollment->id,
                'amount' => $transaction->total,
                'currency' => $transaction->currency,
                'status' => 'completed',
                'provider' => 'paddle',
                'transaction_id' => $transaction->paddle_id,
                'paid_at' => now(),
            ]);

            // Fire Events
            event(new StudentEnrolled($enrollment));
            event(new PaymentReceived($enrollment));

            // Sync Stats
            $course = Course::find($courseId);
            if ($course) {
                UpdateCourseStats::dispatch($course);
            }
        } else {
            Log::warning('Paddle TransactionCompleted sin custom_data válido.', ['payload' => $payload]);
        }
    }
}
