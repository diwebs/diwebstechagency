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
        if (config('cache.default') === 'redis' || config('queue.default') === 'redis' || config('session.driver') === 'redis') {
            try {
                \Illuminate\Support\Facades\Redis::connection()->ping();
            } catch (\Exception $e) {
                if (config('cache.default') === 'redis') {
                    config(['cache.default' => 'file']);
                }
                if (config('queue.default') === 'redis') {
                    config(['queue.default' => 'database']);
                }
                if (config('session.driver') === 'redis') {
                    config(['session.driver' => 'file']);
                }
            }
        }

        // Register global @money blade directive
        \Illuminate\Support\Facades\Blade::directive('money', function ($expression) {
            return "<?php echo \App\Helpers\PaymentHelper::format($expression); ?>";
        });

        // Dynamically override mail configurations from settings.json
        try {
            $mailMailer = \App\Helpers\SettingsHelper::get('mail_mailer');
            if ($mailMailer) {
                config(['mail.default' => $mailMailer]);
            }
            $mailHost = \App\Helpers\SettingsHelper::get('mail_host');
            if ($mailHost) {
                config(['mail.mailers.smtp.host' => $mailHost]);
            }
            $mailPort = \App\Helpers\SettingsHelper::get('mail_port');
            if ($mailPort) {
                config(['mail.mailers.smtp.port' => (int)$mailPort]);
            }
            $mailUsername = \App\Helpers\SettingsHelper::get('mail_username');
            if ($mailUsername) {
                config(['mail.mailers.smtp.username' => $mailUsername]);
            }
            $mailPassword = \App\Helpers\SettingsHelper::get('mail_password');
            if ($mailPassword) {
                config(['mail.mailers.smtp.password' => $mailPassword]);
            }
            $mailScheme = \App\Helpers\SettingsHelper::get('mail_scheme');
            if ($mailScheme) {
                $schemeMap = [
                    'ssl' => 'smtps',
                    'tls' => 'smtp',
                ];
                $mappedScheme = $schemeMap[strtolower($mailScheme)] ?? $mailScheme;
                config(['mail.mailers.smtp.scheme' => $mappedScheme]);
            }
            $mailFromAddress = \App\Helpers\SettingsHelper::get('mail_from_address');
            if ($mailFromAddress) {
                config(['mail.from.address' => $mailFromAddress]);
            }
            $mailFromName = \App\Helpers\SettingsHelper::get('mail_from_name');
            if ($mailFromName) {
                config(['mail.from.name' => $mailFromName]);
            }
        } catch (\Exception $e) {
            // Silently fail if settings helper cannot be resolved during early boot/CLI tasks
        }
    }
}
