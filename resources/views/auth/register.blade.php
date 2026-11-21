@extends('layouts.app')

@section('title', 'Register - NQ Baccarat')

@push('styles')
<style>
    .auth-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 64px - 200px);
        padding: 3rem 1rem;
    }
    .auth-card {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 1rem;
        padding: 2.5rem;
        width: 100%;
        max-width: 420px;
    }
    .auth-card h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; }
    .auth-card .subtitle { color: var(--color-text-muted); font-size: 0.875rem; margin-bottom: 2rem; }
    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.375rem; }
    .form-group input {
        width: 100%;
        padding: 0.625rem 0.75rem;
        background: var(--color-bg);
        border: 1px solid var(--color-border);
        border-radius: 0.5rem;
        color: var(--color-text);
        font-family: var(--font);
        font-size: 0.875rem;
    }
    .form-group input:focus { outline: none; border-color: var(--color-primary); }
    .btn-submit {
        width: 100%;
        padding: 0.75rem;
        margin-top: 0.5rem;
        background: var(--color-primary);
        color: #fff;
        border: none;
        border-radius: 0.5rem;
        font-family: var(--font);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-submit:hover { background: var(--color-primary-hover); }
    .divider {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 1.5rem 0;
        color: var(--color-text-muted);
        font-size: 0.75rem;
    }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--color-border); }
    .social-buttons { display: flex; flex-direction: column; gap: 0.75rem; }
    .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: var(--color-text-muted); }
    .error-msg { color: var(--color-danger); font-size: 0.8rem; margin-top: 0.25rem; }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Create your account</h1>
        <p class="subtitle">Start playing baccarat in seconds</p>

        <div class="social-buttons">
            <a href="{{ route('social.redirect', 'google') }}" class="btn-social btn">
                <svg width="18" height="18" viewBox="0 0 24 24" style="margin-right:0.5rem"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Sign up with Google
            </a>
            <a href="{{ route('social.redirect', 'nikniq') }}" class="btn-social btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-right:0.5rem"><circle cx="12" cy="12" r="10" fill="var(--color-primary)" opacity="0.2"/><text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="var(--color-primary)">N</text></svg>
                Sign up with NikNiq
            </a>
        </div>

        <div class="divider">or register with email</div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                @error('name') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                @error('password') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn-submit">Create Account</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </div>
</div>
@endsection
