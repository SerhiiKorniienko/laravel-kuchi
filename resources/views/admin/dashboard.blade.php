@extends('feedback::layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="bg-orange min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-6xl font-black text-black uppercase mb-4 tracking-tight">
                ADMIN
            </h1>
            <div class="bg-black text-white px-8 py-3 brutalist-border inline-block transform -rotate-1">
                <p class="font-bold uppercase text-lg">Control Center</p>
            </div>
        </div>

        <livewire:feedback-dashboard />
    </div>
</div>
@endsection