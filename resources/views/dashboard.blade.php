@extends('layouts.app')

@section('title', 'Dashboard - NQ Baccarat')

@push('styles')
<style>
    .dashboard { padding: 3rem 1rem; max-width: 800px; margin: 0 auto; }
    .welcome-card {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 1rem;
        padding: 2.5rem;
        text-align: center;
    }
    .welcome-card h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
    .welcome-card p { color: var(--color-text-muted); margin-bottom: 1.5rem; }
    .avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--color-primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0 auto 1rem;
    }
    .avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
</style>
@endpush

@section('content')
<div class="dashboard">
    <div class="welcome-card">
        <div class="avatar">
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">
            @else
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            @endif
        </div>
        <h1>Welcome, {{ auth()->user()->name }}!</h1>
        <p>Your baccarat table is being prepared. The game will be available soon.</p>
        <a href="/" class="btn btn-primary btn-lg">Back to Home</a>
    </div>
</div>
@endsection
