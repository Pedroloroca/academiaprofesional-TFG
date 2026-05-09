<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Laravel\Paddle\Cashier;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function show(Request $request, Course $course)
    {
        $user = $request->user();

        if (!$user->hasRole('student')) {
            $user->assignRole('student');
        }

        $student = Student::firstOrCreate(
            ['user_id' => $user->id]
        );

        if ($student->courses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('admin.courses')->with('error', 'Ya estás matriculado en este curso.');
        }

        $amountInCents = (int) ($course->price * 100);

        // Ensure user exists as a Paddle customer
        $user->createAsCustomer();

        // Create transaction via Paddle API and extract the hosted checkout URL
        $transaction = Cashier::api('POST', 'transactions', [
            'customer_id' => $user->customer->paddle_id,
            'items' => [[
                'price' => [
                    'description' => 'Matrícula: ' . $course->title,
                    'unit_price' => [
                        'amount' => (string) $amountInCents,
                        'currency_code' => config('cashier.currency', 'USD'),
                    ],
                    'product' => [
                        'name' => $course->title,
                        'tax_category' => 'standard',
                    ],
                ],
                'quantity' => 1,
            ]],
            'custom_data' => [
                'course_id' => $course->id,
                'student_id' => $student->id,
            ],
        ])->json()['data'];

        $hostedCheckoutUrl = $transaction['checkout']['url'] ?? null;

        if (!$hostedCheckoutUrl) {
            return back()->with('error', 'No se pudo generar el enlace de pago. Inténtalo de nuevo.');
        }

        return view('checkout.show', [
            'course' => $course,
            'checkoutUrl' => $hostedCheckoutUrl,
            'transaction' => $transaction,
        ]);
    }

    public function success(Course $course)
    {
        return redirect()->route('admin.courses')->with('message', '¡Pago completado! Tu matrícula en ' . $course->title . ' se procesará en breve.');
    }
}
