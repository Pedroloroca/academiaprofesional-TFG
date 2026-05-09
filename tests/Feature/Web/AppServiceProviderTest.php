<?php

use Laravel\Paddle\Events\WebhookReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

test('AppServiceProvider logs paddle webhooks', function () {
    Log::shouldReceive('info')->once()->withArgs(function($message, $payload) {
        return str_contains($message, 'Paddle Webhook Received: test_event') && $payload['event_type'] === 'test_event';
    });

    $payload = ['event_type' => 'test_event'];
    event(new WebhookReceived($payload));
});
