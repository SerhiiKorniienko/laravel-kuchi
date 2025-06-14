<?php

namespace SerhiiKorniienko\LaravelKuchi\Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use SerhiiKorniienko\LaravelKuchi\FeedbackServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
        $this->artisan('migrate', ['--database' => 'testing']);

        // Mock Vite for tests
        $this->mockVite();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            FeedbackServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up authentication configuration
        config()->set('auth.providers.users.model', User::class);

        // Set up package configuration
        config()->set('kuchi.middleware', ['web']);
        config()->set('kuchi.admin_middleware', ['web']);
        config()->set('kuchi.admin_users', ['admin@example.com', 1]);
        config()->set('kuchi.categories', [
            'feature_request' => 'Feature Request',
            'improvement' => 'Improvement',
            'question' => 'Question',
            'other' => 'Other',
        ]);
        config()->set('kuchi.bug_priorities', [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ]);
        config()->set('kuchi.statuses', [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function createUser(array $attributes = []): Authenticatable
    {
        return User::query()->create(array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ], $attributes));
    }

    protected function createAdminUser(array $attributes = []): Authenticatable
    {
        return User::query()->create(array_merge([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ], $attributes));
    }

    /**
     * Mock the Vite helper function for tests
     */
    protected function mockVite(): void
    {
        // Set up a fake Vite manifest
        $this->app->singleton('path.public', fn (): string => __DIR__.'/../vendor/orchestra/testbench-core/laravel/public');

        // Create the build directory if it doesn't exist
        $buildDir = $this->app['path.public'].'/build';
        if (! is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
        }

        // Create assets directory if it doesn't exist
        $assetsDir = $buildDir.'/assets';
        if (! is_dir($assetsDir)) {
            mkdir($assetsDir, 0755, true);
        }

        // Create empty asset files
        file_put_contents($assetsDir.'/app-test.css', '/* Test CSS */');
        file_put_contents($assetsDir.'/app-test.js', '/* Test JS */');

        // Create a more complete manifest file
        $manifestPath = $buildDir.'/manifest.json';
        if (! file_exists($manifestPath)) {
            file_put_contents($manifestPath, json_encode([
                'resources/css/app.css' => [
                    'file' => 'assets/app-test.css',
                    'src' => 'resources/css/app.css',
                    'isEntry' => true,
                ],
                'resources/js/app.js' => [
                    'file' => 'assets/app-test.js',
                    'src' => 'resources/js/app.js',
                    'isEntry' => true,
                ],
            ]));
        }
    }
}
