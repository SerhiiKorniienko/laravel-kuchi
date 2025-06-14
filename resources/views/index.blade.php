@extends('feedback::layouts.app')

@section('title', 'Feedback & Bug Reports')

@section('content')
<div class="bg-lime-green min-h-screen relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="grid grid-cols-8 gap-4 h-full">
            @for($i = 0; $i < 64; $i++)
                <div class="bg-black"></div>
            @endfor
        </div>
    </div>
    
    <div class="relative z-10 max-w-6xl mx-auto px-6 py-16">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-6xl md:text-8xl font-black text-black mb-6 uppercase tracking-tight leading-none">
                MAKE IT<br>
                <span class="text-hot-pink">BETTER!</span>
            </h1>
            <div class="bg-white brutalist-border brutalist-shadow-lg inline-block px-8 py-4 mb-8 transform rotate-1">
                <p class="text-black text-xl font-bold uppercase">
                    Your ideas shape our future
                </p>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-8">
                <div class="bg-yellow brutalist-border brutalist-shadow-lg p-6 transform -rotate-1 max-w-2xl mx-auto">
                    <p class="text-black font-bold text-lg uppercase text-center">
                        {{ session('message') }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col md:flex-row gap-8 justify-center items-center mb-16">
            <livewire:feedback-form />
            <livewire:bug-report-form />
        </div>

        <!-- Feedback Section -->
        <div class="bg-white brutalist-border brutalist-shadow-lg transform rotate-1">
            <div class="bg-purple p-8 brutalist-border-thick border-l-0 border-r-0 border-t-0">
                <h2 class="text-4xl font-black text-white uppercase text-center tracking-wider">
                    COMMUNITY VOICE
                </h2>
                <p class="text-white font-bold text-center mt-2 uppercase text-lg">
                    See what others are saying
                </p>
            </div>
            <livewire:feedback-list />
        </div>
    </div>
</div>
@endsection