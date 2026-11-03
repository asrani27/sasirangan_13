<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
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
