<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Event;
use Laravel\Paddle\Events\TransactionCompleted;
use App\Listeners\HandlePaddleTransactionCompleted;
use Laravel\Paddle\Events\WebhookReceived;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerPolicies();

        Event::listen(
            TransactionCompleted::class,
            HandlePaddleTransactionCompleted::class,
        );

        Event::listen(function (WebhookReceived $event) {
            \Illuminate\Support\Facades\Log::info('Paddle Webhook Received: ' . $event->payload['event_type'], $event->payload);
        });
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Enrollment::class, EnrollmentPolicy::class);
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
