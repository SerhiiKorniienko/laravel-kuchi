<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Feedback') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet" />

    @livewireStyles

    <style>
        body {
            font-family: 'Space Grotesk', monospace;
            font-weight: 500;
        }

        .brutalist-shadow {
            box-shadow: 8px 8px 0px #000000;
        }

        .brutalist-shadow-sm {
            box-shadow: 4px 4px 0px #000000;
        }

        .brutalist-shadow-lg {
            box-shadow: 12px 12px 0px #000000;
        }

        .brutalist-border {
            border: 4px solid #000000;
        }

        .brutalist-border-thick {
            border: 6px solid #000000;
        }

        .brutalist-btn {
            border: 4px solid #000000;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.1s ease;
        }

        .brutalist-btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px #000000;
        }

        .brutalist-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0px #000000;
        }

        .brutalist-input {
            border: 4px solid #000000;
            font-weight: 600;
            background: #ffffff;
        }

        .brutalist-input:focus {
            outline: none;
            box-shadow: 4px 4px 0px #000000;
            transform: translate(-2px, -2px);
        }

        .brutalist-card {
            border: 4px solid #000000;
            background: #ffffff;
        }

        .color-electric-blue { background-color: #00D4FF; }
        .color-hot-pink { background-color: #FF006B; }
        .color-lime-green { background-color: #00FF88; }
        .color-orange { background-color: #FF8800; }
        .color-purple { background-color: #8800FF; }
        .color-yellow { background-color: #FFD700; }
        .color-red { background-color: #FF3333; }

        .text-electric-blue { color: #00D4FF; }
        .text-hot-pink { color: #FF006B; }
        .text-lime-green { color: #00FF88; }
        .text-orange { color: #FF8800; }
        .text-purple { color: #8800FF; }
        .text-yellow { color: #FFD700; }
        .text-red { color: #FF3333; }

        .bg-electric-blue { background-color: #00D4FF; }
        .bg-hot-pink { background-color: #FF006B; }
        .bg-lime-green { background-color: #00FF88; }
        .bg-orange { background-color: #FF8800; }
        .bg-purple { background-color: #8800FF; }
        .bg-yellow { background-color: #FFD700; }
        .bg-red { background-color: #FF3333; }

        .hover\:bg-electric-blue:hover { background-color: #00B8E6; }
        .hover\:bg-hot-pink:hover { background-color: #E6005F; }
        .hover\:bg-lime-green:hover { background-color: #00E67A; }
        .hover\:bg-orange:hover { background-color: #E67700; }
        .hover\:bg-purple:hover { background-color: #7700E6; }
        .hover\:bg-yellow:hover { background-color: #E6C200; }
        .hover\:bg-red:hover { background-color: #E62E2E; }
    </style>
</head>
<body class="bg-white antialiased">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-electric-blue brutalist-border-thick border-t-0 border-l-0 border-r-0 relative z-10">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex justify-between h-20 items-center">
                    <div class="flex items-center">
                        <a href="{{ route('feedback.index') }}" class="text-3xl font-black text-black uppercase tracking-wider">
                            FEEDBACK
                        </a>
                    </div>
                    <div class="flex items-center space-x-6">
                        @auth
                            <div class="bg-yellow brutalist-border px-4 py-2 brutalist-shadow-sm">
                                <span class="text-black font-bold uppercase text-sm">{{ auth()->user()->name }}</span>
                            </div>
                            @if(in_array(auth()->user()->email, config('kuchi.admin_users', [])) || in_array(auth()->id(), config('kuchi.admin_users', [])))
                                <a href="{{ route('feedback.admin.dashboard') }}"
                                   class="bg-hot-pink text-white px-6 py-3 brutalist-btn brutalist-shadow-sm text-sm">
                                    ADMIN
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="relative">
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>
