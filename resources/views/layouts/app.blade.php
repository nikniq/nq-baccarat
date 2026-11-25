<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --color-bg: #0a0e17;
            --color-surface: #111827;
            --color-surface-hover: #1f2937;
            --color-border: #1f2937;
            --color-text: #f9fafb;
            --color-text-muted: #9ca3af;
            --color-primary: #10b981;
            --color-primary-hover: #059669;
            --color-accent: #f59e0b;
            --color-danger: #ef4444;
            --color-card: rgba(17, 24, 39, 0.8);
            --font: 'Inter', system-ui, sans-serif;
        }
        body {
            font-family: var(--font);
            background: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }
        a { color: var(--color-primary); text-decoration: none; }
        a:hover { color: var(--color-primary-hover); }

        /* Navigation */
        .nav {
            background: rgba(10, 14, 23, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--color-border);
            padding: 0 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }
        .nav-brand {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--color-text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-brand span { color: var(--color-primary); }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            list-style: none;
        }
        .nav-links a {
            color: var(--color-text-muted);
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-links a:hover { color: var(--color-text); }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: var(--color-primary);
            color: #fff;
        }
        .btn-primary:hover { background: var(--color-primary-hover); color: #fff; }
        .btn-outline {
            background: transparent;
            color: var(--color-text);
            border: 1px solid var(--color-border);
        }
        .btn-outline:hover { background: var(--color-surface-hover); color: var(--color-text); }
        .btn-social {
            width: 100%;
            padding: 0.75rem 1.25rem;
            background: var(--color-surface);
            color: var(--color-text);
            border: 1px solid var(--color-border);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .btn-social:hover { background: var(--color-surface-hover); color: var(--color-text); }
        .btn-lg { padding: 0.75rem 2rem; font-size: 1rem; }

        /* Footer */
        .footer {
            border-top: 1px solid var(--color-border);
            padding: 3rem 1rem;
            margin-top: auto;
        }
        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 2rem;
        }
        .footer-brand { font-weight: 700; font-size: 1.125rem; margin-bottom: 0.5rem; }
        .footer-brand span { color: var(--color-primary); }
        .footer-desc { color: var(--color-text-muted); font-size: 0.875rem; }
        .footer-heading { font-weight: 600; font-size: 0.875rem; margin-bottom: 0.75rem; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 0.5rem; }
        .footer-links a { color: var(--color-text-muted); font-size: 0.875rem; }
        .footer-links a:hover { color: var(--color-text); }
        .footer-bottom {
            max-width: 1200px;
            margin: 2rem auto 0;
            padding-top: 1.5rem;
            border-top: 1px solid var(--color-border);
            text-align: center;
            color: var(--color-text-muted);
            font-size: 0.8rem;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }

        /* Mobile nav */
        .nav-toggle { display: none; background: none; border: none; color: var(--color-text); font-size: 1.5rem; cursor: pointer; }
        @media (max-width: 768px) {
            .nav-toggle { display: block; }
            .nav-links {
                display: none;
                position: absolute;
                top: 64px;
                left: 0;
                right: 0;
                background: var(--color-bg);
                border-bottom: 1px solid var(--color-border);
                flex-direction: column;
                padding: 1rem;
                gap: 0.75rem;
            }
            .nav-links.open { display: flex; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="nav">
        <div class="nav-inner">
            <a href="/" class="nav-brand">NQ <span>Baccarat</span></a>
            <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('open')">&#9776;</button>
            <ul class="nav-links">
                <li><a href="/#features">Features</a></li>
                <li><a href="/#how-it-works">How It Works</a></li>
                <li><a href="/#faq">FAQ</a></li>
                @auth
                    <li><a href="{{ route('game') }}">Play</a></li>
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-outline">Logout</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="btn btn-outline">Login</a></li>
                    <li><a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-inner">
            <div>
                <div class="footer-brand">NQ <span>Baccarat</span></div>
                <p class="footer-desc">The ultimate online baccarat experience. Play smart, play fair, play with confidence.</p>
            </div>
            <div>
                <div class="footer-heading">Game</div>
                <ul class="footer-links">
                    <li><a href="/#features">Features</a></li>
                    <li><a href="/#how-it-works">How It Works</a></li>
                    <li><a href="/#faq">FAQ</a></li>
                </ul>
            </div>
            <div>
                <div class="footer-heading">Account</div>
                <ul class="footer-links">
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                </ul>
            </div>
            <div>
                <div class="footer-heading">Legal</div>
                <ul class="footer-links">
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Responsible Gaming</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} NQ Baccarat. All rights reserved.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
