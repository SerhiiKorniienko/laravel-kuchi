<?php

use SerhiiKorniienko\LaravelKuchi\Models\BugReport;
use SerhiiKorniienko\LaravelKuchi\Models\Feedback;

return [
    /*
    |--------------------------------------------------------------------------
    | Feedback Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Laravel Feedback package.
    |
    */

    'middleware' => ['web', 'auth'],

    'route_prefix' => 'feedback',

    'models' => [
        'feedback' => Feedback::class,
        'bug_report' => BugReport::class,
    ],

    'admin_middleware' => ['web', 'auth'],

    'admin_users' => [
        // Add user IDs or emails of admin users who can view all feedback
        // 'admin@example.com',
        // 1, // user ID
    ],

    'per_page' => 10,

    'allow_anonymous' => false,

    'categories' => [
        'feature_request' => 'Feature Request',
        'improvement' => 'Improvement',
        'question' => 'Question',
        'other' => 'Other',
    ],

    'bug_priorities' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ],

    'statuses' => [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ],
];
