<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Jobs\MqttSubcriberJob;


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
        if (env('APP_URL')) {
            URL::forceRootUrl(env('APP_URL'));
        }

if (config('database.default') === 'sqlite') {
        // Deteksi apakah ini environment Vercel (Linux)
        $tmpPath = '/tmp/database.sqlite';
        $localPath = database_path('database.sqlite');

        // Jika di Linux (Vercel)
        if (PHP_OS_FAMILY !== 'Windows') {
            if (!file_exists('/tmp')) {
                mkdir('/tmp');
            }

            if (!file_exists($tmpPath)) {
                touch($tmpPath);
            }

            config(['database.connections.sqlite.database' => $tmpPath]);
        } 
        // Jika di lokal (Windows)
        else {
            if (!file_exists($localPath)) {
                touch($localPath);
            }

            config(['database.connections.sqlite.database' => $localPath]);
        }
    }
        // if (env('APP_ENV') !== 'local') {
        //     URL::forceScheme('https');
        // }

        // if (config('database.default') === 'sqlite' && !file_exists(database_path('database.sqlite'))) {
        //     $tmpPath = '/tmp/database.sqlite';
        //     if (!file_exists($tmpPath)) {
        //         touch($tmpPath);
        //     }
        //     config(['database.connections.sqlite.database' => $tmpPath]);
        // }

        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

    //    MqttSubcriberJob::dispatch();

        // if (env('APP_ENV') !== 'local' && !env('DEVTUNNEL')) {
        //     URL::forceScheme('https');
        // }
    }
    // public function boot(): void
    // {
    //    MqttSubcriberJob::dispatch();
    // }
}
