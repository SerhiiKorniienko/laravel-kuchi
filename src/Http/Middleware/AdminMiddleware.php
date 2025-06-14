<?php

namespace SerhiiKorniienko\LaravelKuchi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! Auth::check()) {
            if (! app('router')->has('login')) {
                return redirect()->back()->with('error', 'You need to be logged in to access this page.');
            }

            return redirect()->route('login');
        }

        $user = Auth::user();
        $adminUsers = config('kuchi.admin_users', []);

        if (in_array($user->email, $adminUsers)) {
            return $next($request);
        }

        // Check if user's ID is in the admin_users array (handle both string and integer IDs)
        foreach ($adminUsers as $adminUser) {
            if (is_numeric($adminUser) && (int) $adminUser === (int) $user->id) {
                return $next($request);
            }
        }

        return redirect()->back()->with('error', 'You do not have permission to access this page.');
    }
}
