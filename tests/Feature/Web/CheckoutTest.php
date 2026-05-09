<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('student');
    $this->course = Course::factory()->create(['price' => 100]);
});

test('checkout page shows redirect button to paddle', function () {
    // Mock Paddle API response
    Http::fake([
        'sandbox-api.paddle.com/transactions' => Http::response([
            'data' => [
                'id' => 'txn_123',
                'checkout' => [
                    'url' => 'https://checkout.paddle.com/test'
                ]
            ]
        ], 200),
    ]);

    $this->actingAs($this->user)
        ->get(route('checkout.show', $this->course))
        ->assertStatus(200)
        ->assertViewIs('checkout.show')
        ->assertViewHas('checkoutUrl', 'https://checkout.paddle.com/test');
});

test('checkout success page redirects with message', function () {
    $this->actingAs($this->user)
        ->get(route('checkout.success', $this->course))
        ->assertRedirect(route('admin.courses'))
        ->assertSessionHas('message');
});
