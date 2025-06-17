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
}
