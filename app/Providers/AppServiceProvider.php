<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
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
        // Force HTTPS in production, when APP_URL is HTTPS, on online domains, or when accessed via secure reverse proxy
        if (! $this->app->environment('testing')) {
            $isLocalhost = in_array(request()?->getHost(), ['localhost', '127.0.0.1', '::1', null], true);

            if (
                $this->app->environment('production') ||
                str_starts_with((string) config('app.url'), 'https://') ||
                (! $isLocalhost && ! $this->app->runningInConsole()) ||
                (request() && (
                    request()->isSecure() ||
                    request()->header('x-forwarded-proto') === 'https' ||
                    request()->header('x-forwarded-ssl') === 'on' ||
                    request()->server('HTTPS') === 'on' ||
                    request()->server('SERVER_PORT') == 443
                ))
            ) {
                URL::forceScheme('https');
            }
        }

        // Prohibit all destructive commands (migrate:fresh, migrate:reset, migrate:refresh, db:wipe, migrate:rollback)
        // to prevent tables and data from ever being accidentally dropped or reset.
        DB::prohibitDestructiveCommands(
            ! $this->app->environment('testing') || config('database.default') !== 'sqlite'
        );

        Blade::directive('currency', function ($expression) {
            return "Rp. <?php echo number_format((float) ($expression), 0, ',', '.'); ?>";
        });

        Blade::directive('rupiah', function ($expression) {
            return "Rp. <?php echo number_format((float) ($expression), 0, ',', '.'); ?>";
        });
    }
}
