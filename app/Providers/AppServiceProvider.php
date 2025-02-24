<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Check mail configuration and throw exceptions in development
        if (config('app.env') === 'local') {
            if (empty(config('mail.from.address'))) {
                throw new \Exception('Mail "from" address is not configured');
            }
            if (empty(config('mail.mailers.smtp.host')) || empty(config('mail.mailers.smtp.port'))) {
                throw new \Exception('SMTP configuration is incomplete');
            }
            if (empty(config('mail.contact.recipient'))) {
                throw new \Exception('Contact form recipient email is not configured');
            }
            if (config('mail.default') === 'log') {
                throw new \Exception('Mail driver is set to "log". Emails will not be sent.');
            }
        } else {
            // Log warnings in production
            if (empty(config('mail.from.address'))) {
                \Log::warning('Mail "from" address is not configured');
            }
            if (empty(config('mail.mailers.smtp.host')) || empty(config('mail.mailers.smtp.port'))) {
                \Log::warning('SMTP configuration is incomplete');
            }
            if (empty(config('mail.contact.recipient'))) {
                \Log::warning('Contact form recipient email is not configured');
            }
            if (config('mail.default') === 'log') {
                \Log::warning('Mail driver is set to "log". Emails will not be sent.');
            }
        }
    }
}
