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
<<<<<<< HEAD
        // if (env('APP_URL')) {
        //     URL::forceRootUrl(env('APP_URL'));
        // }
        // if (env('APP_ENV') !== 'local') {
        //     URL::forceScheme('https');
        // }

        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }


=======
        if (env('APP_URL')) {
            URL::forceRootUrl(env('APP_URL'));
        }
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
>>>>>>> e99e323cb4debd3b6f60c8116f8e3510c2d02dea
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
