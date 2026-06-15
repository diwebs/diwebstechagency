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
    }
}
