@extends('layouts.app')

@section('title', 'NQ Baccarat - Play the World\'s Most Elegant Card Game')

@push('styles')
<style>
    .hero {
        text-align: center;
        padding: 6rem 1rem 4rem;
        background: radial-gradient(ellipse at top, rgba(16, 185, 129, 0.08) 0%, transparent 60%);
    }
    .hero-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .hero h1 {
        font-size: clamp(2.5rem, 6vw, 4rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 1.5rem;
    }
    .hero h1 em {
        font-style: normal;
        background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .hero p {
        font-size: 1.125rem;
        color: var(--color-text-muted);
        max-width: 600px;
        margin: 0 auto 2rem;
    }
    .hero-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }

    .cards-display {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 3rem;
    }
    .playing-card {
        width: 80px;
        height: 112px;
        background: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        transform: rotate(var(--rot, 0deg));
        transition: transform 0.3s;
    }
    .playing-card:hover { transform: rotate(0deg) translateY(-8px); }
    .card-red { color: #dc2626; }
    .card-black { color: #111; }

    .features { padding: 5rem 1rem; }
    .section-heading {
        text-align: center;
        margin-bottom: 3rem;
    }
    .section-heading h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .section-heading p { color: var(--color-text-muted); }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    .feature-card {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 1rem;
        padding: 2rem;
        transition: border-color 0.2s;
    }
    .feature-card:hover { border-color: var(--color-primary); }
    .feature-icon {
        width: 48px;
        height: 48px;
        background: rgba(16, 185, 129, 0.1);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .feature-card h3 { font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; }
    .feature-card p { color: var(--color-text-muted); font-size: 0.875rem; }

    .how-it-works { padding: 5rem 1rem; background: rgba(17, 24, 39, 0.5); }
    .steps {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 2rem;
        max-width: 900px;
        margin: 0 auto;
        text-align: center;
    }
    .step-number {
        width: 48px;
        height: 48px;
        background: var(--color-primary);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        margin: 0 auto 1rem;
    }
    .step h3 { font-weight: 600; margin-bottom: 0.5rem; }
    .step p { color: var(--color-text-muted); font-size: 0.875rem; }

    .faq { padding: 5rem 1rem; }
    .faq-list { max-width: 700px; margin: 0 auto; }
    .faq-item {
        border-bottom: 1px solid var(--color-border);
        padding: 1.25rem 0;
    }
    .faq-item summary {
        font-weight: 600;
        cursor: pointer;
        list-style: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .faq-item summary::after { content: '+'; font-size: 1.25rem; color: var(--color-primary); }
    .faq-item[open] summary::after { content: '\2212'; }
    .faq-item p { color: var(--color-text-muted); font-size: 0.875rem; margin-top: 0.75rem; }

    .cta {
        text-align: center;
        padding: 5rem 1rem;
        background: radial-gradient(ellipse at bottom, rgba(16, 185, 129, 0.08) 0%, transparent 60%);
    }
    .cta h2 { font-size: 2rem; font-weight: 700; margin-bottom: 1rem; }
    .cta p { color: var(--color-text-muted); margin-bottom: 2rem; }
</style>
@endpush

@section('content')
<section class="hero">
    <span class="hero-badge">Now in Beta</span>
    <h1>Play <em>Baccarat</em><br>Like Never Before</h1>
    <p>Experience the thrill of the world's most elegant card game with real-time gameplay, beautiful design, and fair odds.</p>
    <div class="hero-actions">
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Start Playing Free</a>
        <a href="#features" class="btn btn-outline btn-lg">Learn More</a>
    </div>
    <div class="cards-display">
        <div class="playing-card card-black" style="--rot: -8deg;">A&spades;</div>
        <div class="playing-card card-red" style="--rot: -3deg;">K&hearts;</div>
        <div class="playing-card card-black" style="--rot: 2deg;">Q&clubs;</div>
        <div class="playing-card card-red" style="--rot: 7deg;">J&diams;</div>
    </div>
</section>

<section class="features" id="features">
    <div class="section-heading">
        <h2>Why NQ Baccarat?</h2>
        <p>Everything you need for the ultimate baccarat experience</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">&#9824;</div>
            <h3>Classic Gameplay</h3>
            <p>Traditional punto banco baccarat with authentic rules and smooth card dealing animations.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#9876;</div>
            <h3>Provably Fair</h3>
            <p>Every hand uses cryptographic verification so you can confirm fairness after each round.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#128202;</div>
            <h3>Live Statistics</h3>
            <p>Track your results with detailed scorecards, big road, and bead plate history boards.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#9889;</div>
            <h3>Instant Play</h3>
            <p>No downloads required. Play instantly in your browser on any device.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#128274;</div>
            <h3>Secure Accounts</h3>
            <p>Sign in with Google or NikNiq for secure, passwordless authentication.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#127942;</div>
            <h3>Leaderboards</h3>
            <p>Compete against players worldwide and climb the rankings with your winning streaks.</p>
        </div>
    </div>
</section>

<section class="how-it-works" id="how-it-works">
    <div class="section-heading">
        <h2>How It Works</h2>
        <p>Get started in three simple steps</p>
    </div>
    <div class="steps">
        <div class="step">
            <div class="step-number">1</div>
            <h3>Create an Account</h3>
            <p>Sign up with your email, Google, or NikNiq account in seconds.</p>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <h3>Choose Your Table</h3>
            <p>Pick from various table limits and find the perfect seat for your style.</p>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <h3>Place Your Bets</h3>
            <p>Bet on Player, Banker, or Tie and watch the cards unfold in real time.</p>
        </div>
    </div>
</section>

<section class="faq" id="faq">
    <div class="section-heading">
        <h2>Frequently Asked Questions</h2>
    </div>
    <div class="faq-list">
        <details class="faq-item">
            <summary>What is baccarat?</summary>
            <p>Baccarat is a comparing card game played between the "Player" and the "Banker." Each round has three possible outcomes: Player wins, Banker wins, or Tie. It's one of the oldest and most popular casino games worldwide.</p>
        </details>
        <details class="faq-item">
            <summary>Is NQ Baccarat free to play?</summary>
            <p>Yes! You can create an account and play with virtual chips completely free. We also offer premium features for dedicated players.</p>
        </details>
        <details class="faq-item">
            <summary>How do I know the game is fair?</summary>
            <p>Every hand is provably fair using cryptographic hashing. After each round, you can verify the seed and hash to confirm the outcome was predetermined and untampered.</p>
        </details>
        <details class="faq-item">
            <summary>Can I play on my phone?</summary>
            <p>Absolutely. NQ Baccarat is fully responsive and works on any modern browser — desktop, tablet, or mobile.</p>
        </details>
        <details class="faq-item">
            <summary>What sign-in methods are supported?</summary>
            <p>You can register with an email and password, or use Google or NikNiq OAuth for quick, secure access.</p>
        </details>
    </div>
</section>

<section class="cta">
    <h2>Ready to Deal?</h2>
    <p>Join thousands of players and experience baccarat at its finest.</p>
    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Create Free Account</a>
</section>
@endsection
