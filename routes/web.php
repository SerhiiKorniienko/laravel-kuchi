<?php

use Illuminate\Support\Facades\Route;

Route::middleware(config('kuchi.middleware', ['web', 'auth']))
    ->prefix(config('kuchi.route_prefix', 'feedback'))
    ->name('feedback.')
    ->group(function (): void {
        Route::get('/', fn () => view('feedback::index'))->name('index');

        Route::middleware(array_merge(config('kuchi.admin_middleware', ['web', 'auth']), ['admin']))
            ->prefix('admin')
            ->name('admin.')
            ->group(function (): void {
                Route::get('/', fn () => view('feedback::admin.dashboard'))->name('dashboard');
            });
    });
