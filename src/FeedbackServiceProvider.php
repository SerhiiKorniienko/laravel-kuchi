<?php

namespace SerhiiKorniienko\LaravelKuchi;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use SerhiiKorniienko\LaravelKuchi\Http\Livewire\BugReportForm;
use SerhiiKorniienko\LaravelKuchi\Http\Livewire\FeedbackDashboard;
use SerhiiKorniienko\LaravelKuchi\Http\Livewire\FeedbackForm;
use SerhiiKorniienko\LaravelKuchi\Http\Livewire\FeedbackList;
use SerhiiKorniienko\LaravelKuchi\Http\Middleware\AdminMiddleware;

class FeedbackServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/kuchi.php', 'kuchi'
        );
    }

    public function boot(): void
    {
        // Register middleware
        $this->app['router']->aliasMiddleware('admin', AdminMiddleware::class);

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'feedback');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Register Livewire components
        Livewire::component('bug-report-form', BugReportForm::class);
        Livewire::component('feedback-form', FeedbackForm::class);
        Livewire::component('feedback-list', FeedbackList::class);
        Livewire::component('feedback-dashboard', FeedbackDashboard::class);

        // Publishable assets
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/kuchi.php' => config_path('kuchi.php'),
            ], 'kuchi-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'kuchi-migrations');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/feedback'),
            ], 'kuchi-views');

            $this->publishes([
                __DIR__.'/../resources/css' => public_path('vendor/feedback/css'),
                __DIR__.'/../resources/js' => public_path('vendor/feedback/js'),
            ], 'kuchi-assets');
        }
    }
}
