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
        \Carbon\Carbon::setLocale('id');

        if (! $this->app->runningInConsole() && request()->hasHeader('Host')) {
            $root = request()->getSchemeAndHttpHost();
            $basePath = str_replace('\\', '/', dirname(request()->getScriptName()));

            if ($basePath !== '/' && $basePath !== '.') {
                $root .= rtrim($basePath, '/');
            }

            \Illuminate\Support\Facades\URL::forceRootUrl($root);
        }
    }
}
