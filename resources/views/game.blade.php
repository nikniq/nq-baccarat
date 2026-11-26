@extends('layouts.app')

@section('title', 'Play Baccarat - NQ Baccarat')

@push('styles')
<style>
    .game-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 1.5rem 1rem;
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 1.5rem;
        min-height: calc(100vh - 64px - 200px);
    }
    @media (max-width: 900px) {
        .game-container { grid-template-columns: 1fr; }
    }

    /* ── Table ── */
    .table-area {
        background:
            radial-gradient(ellipse at 50% 30%, rgba(30,80,50,0.6) 0%, transparent 60%),
            radial-gradient(ellipse at center, #1a3a2a 0%, #0f2318 60%, #081a0e 100%);
        border: 4px solid #3d6a4d;
        border-radius: 1.5rem;
        padding: 2rem 1.5rem;
        box-shadow:
            inset 0 0 80px rgba(0,0,0,0.5),
            inset 0 2px 0 rgba(255,255,255,0.05),
            0 12px 48px rgba(0,0,0,0.5),
            0 0 0 1px rgba(0,0,0,0.3);
        position: relative;
        overflow: hidden;
    }
    .table-area::before {
        content: '';
        position: absolute;
        inset: 10px;
        border: 1.5px solid rgba(255,215,0,0.12);
        border-radius: 1.25rem;
        pointer-events: none;
    }
    .table-area::after {
        content: '';
        position: absolute;
        inset: 14px;
        border: 1px solid rgba(255,215,0,0.06);
        border-radius: 1.15rem;
        pointer-events: none;
    }
    .table-felt-pattern {
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 80%, rgba(255,215,0,0.02) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255,215,0,0.02) 0%, transparent 50%);
        pointer-events: none;
    }

    /* Ambient particles */
    .particles {
        position: absolute;
        inset: 0;
        pointer-events: none;
        overflow: hidden;
    }
    .particle {
        position: absolute;
        width: 2px;
        height: 2px;
        background: rgba(255,215,0,0.3);
        border-radius: 50%;
        animation: particleFloat linear infinite;
    }
    @keyframes particleFloat {
        0% { transform: translateY(0) scale(1); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(-200px) scale(0); opacity: 0; }
    }

    /* ── Shoe indicator ── */
    .shoe-indicator {
        position: absolute;
        top: 12px;
        right: 16px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.65rem;
        color: rgba(255,255,255,0.35);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .shoe-bar {
        width: 60px;
        height: 6px;
        background: rgba(0,0,0,0.3);
        border-radius: 3px;
        overflow: hidden;
    }
    .shoe-fill {
        height: 100%;
        background: linear-gradient(90deg, #ffd700, #f59e0b);
        border-radius: 3px;
        transition: width 0.3s;
    }

    /* ── Status ── */
    .game-status {
        text-align: center;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 2;
    }
    .game-status .round {
        font-size: 0.7rem;
        color: rgba(255,255,255,0.4);
        text-transform: uppercase;
        letter-spacing: 0.15em;
        font-weight: 600;
    }
    .game-status .message {
        font-size: 1.125rem;
        font-weight: 700;
        color: #ffd700;
        min-height: 1.75rem;
        text-shadow: 0 0 20px rgba(255,215,0,0.3);
    }

    /* ── Hands ── */
    .hands {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 1rem;
        align-items: start;
        margin-bottom: 2rem;
        position: relative;
        z-index: 2;
    }
    .hand { text-align: center; }
    .hand-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        margin-bottom: 0.75rem;
        text-shadow: 0 2px 8px rgba(0,0,0,0.5);
    }
    .hand-player .hand-label { color: #60a5fa; }
    .hand-banker .hand-label { color: #f87171; }
    .hand-score {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        font-size: 1.3rem;
        font-weight: 800;
        margin-top: 0.75rem;
        transition: all 0.3s;
    }
    .hand-player .hand-score {
        background: rgba(96,165,250,0.15);
        color: #60a5fa;
        border: 2px solid rgba(96,165,250,0.4);
        box-shadow: 0 0 16px rgba(96,165,250,0.15);
    }
    .hand-banker .hand-score {
        background: rgba(248,113,113,0.15);
        color: #f87171;
        border: 2px solid rgba(248,113,113,0.4);
        box-shadow: 0 0 16px rgba(248,113,113,0.15);
    }
    .hand-score.winner {
        transform: scale(1.2);
        box-shadow: 0 0 30px rgba(255,215,0,0.4);
        border-color: #ffd700;
    }
    .vs {
        font-size: 0.85rem;
        font-weight: 800;
        color: rgba(255,255,255,0.15);
        align-self: center;
        padding-top: 2rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    /* ── Cards ── */
    .cards-row {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        min-height: 110px;
        perspective: 800px;
    }
    .card {
        width: 72px;
        height: 100px;
        border-radius: 8px;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-front, .card-back {
        position: absolute;
        inset: 0;
        border-radius: 8px;
        backface-visibility: hidden;
        overflow: hidden;
    }
    .card-front {
        background: linear-gradient(145deg, #ffffff 0%, #f8f8f8 50%, #f0f0f0 100%);
        box-shadow:
            0 2px 8px rgba(0,0,0,0.2),
            0 8px 24px rgba(0,0,0,0.15),
            inset 0 1px 0 rgba(255,255,255,0.8);
        display: flex;
        flex-direction: column;
        padding: 5px;
    }
    .card-corner {
        display: flex;
        flex-direction: column;
        align-items: center;
        line-height: 1;
    }
    .card-corner-top { align-self: flex-start; }
    .card-corner-bottom {
        align-self: flex-end;
        transform: rotate(180deg);
        margin-top: auto;
    }
    .card-corner .c-rank { font-size: 0.75rem; font-weight: 800; }
    .card-corner .c-suit { font-size: 0.65rem; margin-top: -1px; }
    .card-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.8rem;
        line-height: 1;
    }
    .card-face-art {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2rem;
        line-height: 1;
        opacity: 0.9;
    }
    .card.red .c-rank, .card.red .c-suit, .card.red .card-center, .card.red .card-face-art { color: #c41e3a; }
    .card.black .c-rank, .card.black .c-suit, .card.black .card-center, .card.black .card-face-art { color: #1a1a2e; }
    .card-front .card-sheen {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(135deg, transparent 40%, rgba(255,255,255,0.15) 50%, transparent 60%);
        pointer-events: none;
    }
    .card-back {
        background: linear-gradient(135deg, #1e3a6e, #2a5298);
        border: 2.5px solid #4a7bd4;
        transform: rotateY(180deg);
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    }
    .card-back-pattern {
        position: absolute;
        inset: 4px;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 4px;
        background:
            repeating-linear-gradient(45deg, transparent, transparent 4px, rgba(255,255,255,0.03) 4px, rgba(255,255,255,0.03) 8px),
            repeating-linear-gradient(-45deg, transparent, transparent 4px, rgba(255,255,255,0.03) 4px, rgba(255,255,255,0.03) 8px);
    }
    .card-back-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.1rem;
        font-weight: 900;
        color: rgba(255,255,255,0.15);
        letter-spacing: 0.05em;
    }
    .card.face-down { transform: rotateY(180deg); }
    .card.dealing {
        animation: cardSlideIn 0.5s cubic-bezier(0.22, 1, 0.36, 1);
    }
    @keyframes cardSlideIn {
        from { opacity: 0; transform: translateX(80px) translateY(-30px) rotate(10deg) scale(0.8); }
        to { opacity: 1; transform: translateX(0) translateY(0) rotate(0deg) scale(1); }
    }
    .card.dealing.face-down {
        animation: cardSlideInFlipped 0.5s cubic-bezier(0.22, 1, 0.36, 1);
    }
    @keyframes cardSlideInFlipped {
        from { opacity: 0; transform: translateX(80px) translateY(-30px) rotate(10deg) scale(0.8) rotateY(180deg); }
        to { opacity: 1; transform: translateX(0) translateY(0) rotate(0deg) scale(1) rotateY(180deg); }
    }
    .card.reveal {
        animation: cardReveal 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes cardReveal {
        from { transform: rotateY(180deg); }
        to { transform: rotateY(0deg); }
    }

    /* ── Betting area ── */
    .betting-area {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }
    .bet-spot {
        width: 148px;
        padding: 1rem 0.5rem 0.75rem;
        border-radius: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }
    .bet-spot::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 0.9rem;
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none;
    }
    .bet-spot:hover { transform: translateY(-3px); }
    .bet-spot:hover::before { opacity: 1; }
    .bet-spot.selected {
        border-color: #ffd700;
        box-shadow: 0 0 24px rgba(255,215,0,0.25), inset 0 0 20px rgba(255,215,0,0.05);
    }
    .bet-spot.disabled { opacity: 0.4; pointer-events: none; }
    .bet-player {
        background: linear-gradient(180deg, rgba(96,165,250,0.12) 0%, rgba(96,165,250,0.2) 100%);
    }
    .bet-player::before { background: radial-gradient(ellipse at center, rgba(96,165,250,0.15), transparent 70%); }
    .bet-player.selected { background: linear-gradient(180deg, rgba(96,165,250,0.2) 0%, rgba(96,165,250,0.35) 100%); }
    .bet-tie {
        background: linear-gradient(180deg, rgba(34,197,94,0.12) 0%, rgba(34,197,94,0.2) 100%);
    }
    .bet-tie::before { background: radial-gradient(ellipse at center, rgba(34,197,94,0.15), transparent 70%); }
    .bet-tie.selected { background: linear-gradient(180deg, rgba(34,197,94,0.2) 0%, rgba(34,197,94,0.35) 100%); }
    .bet-banker {
        background: linear-gradient(180deg, rgba(248,113,113,0.12) 0%, rgba(248,113,113,0.2) 100%);
    }
    .bet-banker::before { background: radial-gradient(ellipse at center, rgba(248,113,113,0.15), transparent 70%); }
    .bet-banker.selected { background: linear-gradient(180deg, rgba(248,113,113,0.2) 0%, rgba(248,113,113,0.35) 100%); }
    .bet-spot .label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 0.25rem;
    }
    .bet-player .label { color: #60a5fa; }
    .bet-tie .label { color: #22c55e; }
    .bet-banker .label { color: #f87171; }
    .bet-spot .payout { font-size: 0.6rem; color: rgba(255,255,255,0.35); font-weight: 500; }
    .bet-spot .amount {
        font-size: 1.3rem;
        font-weight: 800;
        color: #ffd700;
        margin-top: 0.25rem;
        min-height: 1.5rem;
        text-shadow: 0 0 10px rgba(255,215,0,0.3);
    }
    .chip-stack {
        display: flex;
        justify-content: center;
        margin-top: 0.25rem;
        min-height: 20px;
    }
    .chip-stack .mini-chip {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 1.5px solid rgba(255,255,255,0.3);
        margin-left: -6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .chip-stack .mini-chip:first-child { margin-left: 0; }

    /* ── Chips ── */
    .chips-row {
        display: flex;
        justify-content: center;
        gap: 0.6rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }
    .chip {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 800;
        cursor: pointer;
        border: 3px dashed rgba(255,255,255,0.25);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        color: #fff;
        text-shadow: 0 1px 3px rgba(0,0,0,0.6);
        position: relative;
        overflow: hidden;
    }
    .chip::before {
        content: '';
        position: absolute;
        inset: 4px;
        border-radius: 50%;
        border: 1.5px dashed rgba(255,255,255,0.15);
        pointer-events: none;
    }
    .chip::after {
        content: '';
        position: absolute;
        top: 15%;
        left: 25%;
        width: 50%;
        height: 25%;
        background: linear-gradient(180deg, rgba(255,255,255,0.2), transparent);
        border-radius: 50%;
        pointer-events: none;
    }
    .chip:hover { transform: scale(1.15) translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.3); }
    .chip.active {
        border-style: solid;
        border-color: #ffd700;
        transform: scale(1.2) translateY(-3px);
        box-shadow: 0 0 16px rgba(255,215,0,0.4), 0 8px 20px rgba(0,0,0,0.3);
    }
    .chip-1  { background: radial-gradient(circle at 35% 35%, #888, #444); }
    .chip-5  { background: radial-gradient(circle at 35% 35%, #f87171, #991b1b); }
    .chip-10 { background: radial-gradient(circle at 35% 35%, #60a5fa, #1e3a8a); }
    .chip-25 { background: radial-gradient(circle at 35% 35%, #4ade80, #166534); }
    .chip-50 { background: radial-gradient(circle at 35% 35%, #fbbf24, #92400e); }
    .chip-100 { background: radial-gradient(circle at 35% 35%, #333, #000); border-color: rgba(255,215,0,0.4); }

    /* ── Controls ── */
    .controls {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }
    .controls .btn { min-width: 100px; }
    .btn-deal {
        background: linear-gradient(180deg, #ffd700, #e6a800);
        color: #111;
        font-weight: 800;
        box-shadow: 0 4px 16px rgba(255,215,0,0.3);
    }
    .btn-deal:hover { background: linear-gradient(180deg, #ffe44d, #ffd700); color: #111; box-shadow: 0 6px 24px rgba(255,215,0,0.4); }
    .btn-deal:disabled { opacity: 0.35; cursor: not-allowed; box-shadow: none; }
    .btn-clear { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.7); }
    .btn-clear:hover { background: rgba(255,255,255,0.15); color: #fff; }
    .btn-rebet { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.25); }
    .btn-rebet:hover { background: rgba(245,158,11,0.25); color: #f59e0b; }

    /* ── Balance bar ── */
    .balance-bar {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2rem;
        padding: 0.75rem;
        margin-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.08);
        font-size: 0.8rem;
        position: relative;
        z-index: 2;
    }
    .balance-bar .label { color: rgba(255,255,255,0.4); font-weight: 500; }
    .balance-bar .value { font-weight: 700; color: #ffd700; text-shadow: 0 0 8px rgba(255,215,0,0.2); }
    .balance-bar .win-value { color: #4ade80; text-shadow: 0 0 8px rgba(74,222,128,0.3); }
    .balance-bar .loss-value { color: #f87171; text-shadow: 0 0 8px rgba(248,113,113,0.3); }

    /* ── Result overlay ── */
    .result-banner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        padding: 1.25rem 3.5rem;
        border-radius: 1rem;
        font-size: 1.5rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        z-index: 20;
        pointer-events: none;
        opacity: 0;
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .result-banner.show {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    .result-banner.player-wins {
        background: linear-gradient(135deg, rgba(59,130,246,0.95), rgba(96,165,250,0.95));
        color: #fff;
        box-shadow: 0 0 60px rgba(96,165,250,0.5), 0 0 120px rgba(96,165,250,0.2);
    }
    .result-banner.banker-wins {
        background: linear-gradient(135deg, rgba(239,68,68,0.95), rgba(248,113,113,0.95));
        color: #fff;
        box-shadow: 0 0 60px rgba(248,113,113,0.5), 0 0 120px rgba(248,113,113,0.2);
    }
    .result-banner.tie-wins {
        background: linear-gradient(135deg, rgba(22,163,74,0.95), rgba(34,197,94,0.95));
        color: #fff;
        box-shadow: 0 0 60px rgba(34,197,94,0.5), 0 0 120px rgba(34,197,94,0.2);
    }
    .result-sub {
        font-size: 0.75rem;
        font-weight: 600;
        opacity: 0.8;
        margin-top: 0.25rem;
        letter-spacing: 0.05em;
    }

    /* Win particles burst */
    .win-burst {
        position: absolute;
        top: 50%;
        left: 50%;
        pointer-events: none;
        z-index: 15;
    }
    .win-spark {
        position: absolute;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        animation: sparkBurst 0.8s ease-out forwards;
    }
    @keyframes sparkBurst {
        from { transform: translate(0,0) scale(1); opacity: 1; }
        to { transform: translate(var(--tx), var(--ty)) scale(0); opacity: 0; }
    }

    /* ── Sidebar ── */
    .sidebar { display: flex; flex-direction: column; gap: 1rem; }

    /* Scoreboard */
    .scoreboard {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 1rem;
        padding: 1rem;
    }
    .scoreboard h3 {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--color-text-muted);
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .scoreboard h3::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 2px;
        background: linear-gradient(135deg, #3b82f6, #ef4444);
    }
    .bead-road {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 3px;
    }
    .bead {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.08);
        transition: all 0.3s;
    }
    .bead.empty { background: rgba(255,255,255,0.02); }
    .bead.player {
        background: radial-gradient(circle at 35% 35%, #60a5fa, #2563eb);
        border-color: #3b82f6;
        box-shadow: 0 0 4px rgba(59,130,246,0.3);
    }
    .bead.banker {
        background: radial-gradient(circle at 35% 35%, #f87171, #dc2626);
        border-color: #ef4444;
        box-shadow: 0 0 4px rgba(239,68,68,0.3);
    }
    .bead.tie {
        background: radial-gradient(circle at 35% 35%, #4ade80, #16a34a);
        border-color: #22c55e;
        box-shadow: 0 0 4px rgba(34,197,94,0.3);
    }
    .bead.latest {
        animation: beadPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes beadPop {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }

    /* Big Road */
    .big-road-panel {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 1rem;
        padding: 1rem;
    }
    .big-road-panel h3 {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--color-text-muted);
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .big-road-panel h3::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: 2px solid #60a5fa;
    }
    .big-road {
        display: grid;
        grid-template-columns: repeat(16, 1fr);
        grid-template-rows: repeat(6, 1fr);
        gap: 2px;
    }
    .big-road-cell {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 50%;
    }
    .big-road-cell.empty { background: transparent; }
    .big-road-cell.player {
        border: 2px solid #3b82f6;
        box-shadow: inset 0 0 2px rgba(59,130,246,0.3);
    }
    .big-road-cell.banker {
        border: 2px solid #ef4444;
        box-shadow: inset 0 0 2px rgba(239,68,68,0.3);
    }
    .big-road-cell.tie-mark {
        position: relative;
    }
    .big-road-cell.tie-mark::after {
        content: '';
        position: absolute;
        inset: 30%;
        background: #22c55e;
        border-radius: 50%;
    }

    /* Stats */
    .stats-panel {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 1rem;
        padding: 1rem;
    }
    .stats-panel h3 {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--color-text-muted);
        margin-bottom: 0.75rem;
    }
    .stat-bars { margin-bottom: 0.75rem; }
    .stat-bar-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
    }
    .stat-bar-label { width: 20px; font-weight: 700; text-align: center; }
    .stat-bar-track {
        flex: 1;
        height: 8px;
        background: rgba(255,255,255,0.05);
        border-radius: 4px;
        overflow: hidden;
    }
    .stat-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: 0;
    }
    .stat-bar-fill.player { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .stat-bar-fill.banker { background: linear-gradient(90deg, #dc2626, #f87171); }
    .stat-bar-fill.tie { background: linear-gradient(90deg, #16a34a, #4ade80); }
    .stat-bar-count { width: 32px; text-align: right; font-weight: 600; font-size: 0.7rem; }
    .stat-row {
        display: flex;
        justify-content: space-between;
        padding: 0.3rem 0;
        font-size: 0.75rem;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .stat-row:last-child { border-bottom: none; }
    .stat-label { color: var(--color-text-muted); }
    .stat-value { font-weight: 600; }
    .stat-player { color: #60a5fa; }
    .stat-banker { color: #f87171; }
    .stat-tie { color: #22c55e; }

    /* History */
    .history-panel {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 1rem;
        padding: 1rem;
    }
    .history-panel h3 {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--color-text-muted);
        margin-bottom: 0.75rem;
    }
    .history-entry {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.35rem 0;
        font-size: 0.72rem;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .history-entry:last-child { border-bottom: none; }
    .history-round { color: var(--color-text-muted); font-size: 0.65rem; }
    .history-result { font-weight: 600; }
    .history-payout { font-weight: 700; }
    .history-payout.win { color: #4ade80; }
    .history-payout.loss { color: #f87171; }

    .natural {
        font-size: 0.6rem;
        font-weight: 700;
        color: #ffd700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 0.25rem;
        text-shadow: 0 0 8px rgba(255,215,0,0.4);
    }
</style>
@endpush

@section('content')
<div class="game-container">
    <div class="table-area" id="table">
        <div class="table-felt-pattern"></div>
        <div class="particles" id="particles"></div>
        <div class="result-banner" id="resultBanner"></div>
        <div class="win-burst" id="winBurst"></div>

        <div class="shoe-indicator">
            <span>Shoe</span>
            <div class="shoe-bar"><div class="shoe-fill" id="shoeFill" style="width:100%"></div></div>
            <span id="shoeCount">416</span>
        </div>

        <div class="game-status">
            <div class="round" id="roundLabel">Round 1</div>
            <div class="message" id="statusMessage">Place your bets</div>
        </div>

        <div class="hands">
            <div class="hand hand-player">
                <div class="hand-label">Player</div>
                <div class="cards-row" id="playerCards"></div>
                <div class="hand-score" id="playerScore" style="visibility:hidden">0</div>
                <div class="natural" id="playerNatural"></div>
            </div>
            <div class="vs">VS</div>
            <div class="hand hand-banker">
                <div class="hand-label">Banker</div>
                <div class="cards-row" id="bankerCards"></div>
                <div class="hand-score" id="bankerScore" style="visibility:hidden">0</div>
                <div class="natural" id="bankerNatural"></div>
            </div>
        </div>

        <div class="betting-area">
            <div class="bet-spot bet-player" id="betPlayer" onclick="selectBetSpot('player')">
                <div class="label">Player</div>
                <div class="payout">Pays 1:1</div>
                <div class="amount" id="betPlayerAmount"></div>
                <div class="chip-stack" id="stackPlayer"></div>
            </div>
            <div class="bet-spot bet-tie" id="betTie" onclick="selectBetSpot('tie')">
                <div class="label">Tie</div>
                <div class="payout">Pays 8:1</div>
                <div class="amount" id="betTieAmount"></div>
                <div class="chip-stack" id="stackTie"></div>
            </div>
            <div class="bet-spot bet-banker" id="betBanker" onclick="selectBetSpot('banker')">
                <div class="label">Banker</div>
                <div class="payout">Pays 0.95:1</div>
                <div class="amount" id="betBankerAmount"></div>
                <div class="chip-stack" id="stackBanker"></div>
            </div>
        </div>

        <div class="chips-row" id="chipsRow">
            <div class="chip chip-1" onclick="selectChip(1, this)" data-value="1">1</div>
            <div class="chip chip-5" onclick="selectChip(5, this)" data-value="5">5</div>
            <div class="chip chip-10 active" onclick="selectChip(10, this)" data-value="10">10</div>
            <div class="chip chip-25" onclick="selectChip(25, this)" data-value="25">25</div>
            <div class="chip chip-50" onclick="selectChip(50, this)" data-value="50">50</div>
            <div class="chip chip-100" onclick="selectChip(100, this)" data-value="100">100</div>
        </div>

        <div class="controls">
            <button class="btn btn-clear" id="btnClear" onclick="clearBets()">Clear</button>
            <button class="btn btn-rebet" id="btnRebet" onclick="rebet()" style="display:none">Rebet</button>
            <button class="btn btn-deal btn-lg" id="btnDeal" onclick="deal()" disabled>Deal</button>
            <button class="btn btn-primary btn-lg" id="btnNewRound" onclick="newRound()" style="display:none">New Round</button>
        </div>

        <div class="balance-bar">
            <div><span class="label">Balance </span><span class="value" id="balance">1,000</span></div>
            <div><span class="label">Bet </span><span class="value" id="totalBet">0</span></div>
            <div><span class="label">Session </span><span class="value" id="sessionProfit">+0</span></div>
        </div>
    </div>

    <div class="sidebar">
        <div class="scoreboard">
            <h3>Bead Road</h3>
            <div class="bead-road" id="beadRoad"></div>
        </div>

        <div class="big-road-panel">
            <h3>Big Road</h3>
            <div class="big-road" id="bigRoad"></div>
        </div>

        <div class="stats-panel">
            <h3>Statistics</h3>
            <div class="stat-bars">
                <div class="stat-bar-row">
                    <span class="stat-bar-label stat-player">P</span>
                    <div class="stat-bar-track"><div class="stat-bar-fill player" id="barPlayer" style="width:0"></div></div>
                    <span class="stat-bar-count stat-player" id="statPlayer">0</span>
                </div>
                <div class="stat-bar-row">
                    <span class="stat-bar-label stat-banker">B</span>
                    <div class="stat-bar-track"><div class="stat-bar-fill banker" id="barBanker" style="width:0"></div></div>
                    <span class="stat-bar-count stat-banker" id="statBanker">0</span>
                </div>
                <div class="stat-bar-row">
                    <span class="stat-bar-label stat-tie">T</span>
                    <div class="stat-bar-track"><div class="stat-bar-fill tie" id="barTie" style="width:0"></div></div>
                    <span class="stat-bar-count stat-tie" id="statTie">0</span>
                </div>
            </div>
            <div class="stat-row">
                <span class="stat-label">Hands</span>
                <span class="stat-value" id="statHands">0</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Player %</span>
                <span class="stat-value stat-player" id="statPlayerPct">0%</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Banker %</span>
                <span class="stat-value stat-banker" id="statBankerPct">0%</span>
            </div>
        </div>

        <div class="history-panel">
            <h3>Recent Hands</h3>
            <div id="historyList"><p style="color:var(--color-text-muted);font-size:0.7rem;">No hands played yet</p></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const SUITS = ['♠','♥','♦','♣'];
    const RANKS = ['A','2','3','4','5','6','7','8','9','10','J','Q','K'];
    const VALUES = [1,2,3,4,5,6,7,8,9,0,0,0,0];
    const FACE_ART = { J: '♞', Q: '♛', K: '♚' };
    const CHIP_COLORS = {
        1: '#555', 5: '#dc2626', 10: '#2563eb', 25: '#16a34a', 50: '#d97706', 100: '#111'
    };

    let shoe = [];
    let balance = 1000;
    let bets = { player: 0, banker: 0, tie: 0 };
    let betChipHistory = { player: [], banker: [], tie: [] };
    let lastBets = null;
    let lastBetChipHistory = null;
    let selectedChip = 10;
    let roundNumber = 1;
    let sessionProfit = 0;
    let stats = { hands: 0, player: 0, banker: 0, tie: 0 };
    let beadResults = [];
    let bigRoadData = [];
    let history = [];
    let isDealing = false;

    function fmt(n) { return n.toLocaleString(); }

    function createShoe() {
        shoe = [];
        for (let d = 0; d < 8; d++) {
            for (let s = 0; s < 4; s++) {
                for (let r = 0; r < 13; r++) {
                    shoe.push({ rank: RANKS[r], suit: SUITS[s], value: VALUES[r] });
                }
            }
        }
        for (let i = shoe.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shoe[i], shoe[j]] = [shoe[j], shoe[i]];
        }
        updateShoeIndicator();
    }

    function drawCard() {
        if (shoe.length < 20) createShoe();
        const card = shoe.pop();
        updateShoeIndicator();
        return card;
    }

    function updateShoeIndicator() {
        const pct = (shoe.length / 416) * 100;
        document.getElementById('shoeFill').style.width = pct + '%';
        document.getElementById('shoeCount').textContent = shoe.length;
    }

    function handTotal(cards) {
        return cards.reduce((s, c) => s + c.value, 0) % 10;
    }

    function isRed(suit) { return suit === '♥' || suit === '♦'; }
    function isFace(rank) { return rank === 'J' || rank === 'Q' || rank === 'K'; }

    function renderCard(card) {
        const color = isRed(card.suit) ? 'red' : 'black';
        const face = isFace(card.rank);
        const centerHtml = face
            ? `<div class="card-face-art">${FACE_ART[card.rank]}</div>`
            : `<div class="card-center">${card.suit}</div>`;

        return `<div class="card ${color} dealing">
            <div class="card-front">
                <div class="card-corner card-corner-top">
                    <span class="c-rank">${card.rank}</span>
                    <span class="c-suit">${card.suit}</span>
                </div>
                ${centerHtml}
                <div class="card-corner card-corner-bottom">
                    <span class="c-rank">${card.rank}</span>
                    <span class="c-suit">${card.suit}</span>
                </div>
                <div class="card-sheen"></div>
            </div>
            <div class="card-back">
                <div class="card-back-pattern"></div>
                <div class="card-back-logo">NQ</div>
            </div>
        </div>`;
    }

    function selectChip(value, el) {
        if (isDealing) return;
        selectedChip = value;
        document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
    }
    window.selectChip = selectChip;

    function selectBetSpot(spot) {
        if (isDealing) return;
        if (selectedChip > balance) return;
        bets[spot] += selectedChip;
        betChipHistory[spot].push(selectedChip);
        balance -= selectedChip;
        updateUI();
    }
    window.selectBetSpot = selectBetSpot;

    function clearBets() {
        if (isDealing) return;
        balance += bets.player + bets.banker + bets.tie;
        bets = { player: 0, banker: 0, tie: 0 };
        betChipHistory = { player: [], banker: [], tie: [] };
        updateUI();
    }
    window.clearBets = clearBets;

    function rebet() {
        if (isDealing || !lastBets) return;
        const total = lastBets.player + lastBets.banker + lastBets.tie;
        if (total > balance) return;
        bets = { ...lastBets };
        betChipHistory = {
            player: [...lastBetChipHistory.player],
            banker: [...lastBetChipHistory.banker],
            tie: [...lastBetChipHistory.tie]
        };
        balance -= total;
        updateUI();
    }
    window.rebet = rebet;

    function totalBet() { return bets.player + bets.banker + bets.tie; }

    function renderChipStack(chips) {
        return chips.slice(-5).map(v =>
            `<div class="mini-chip" style="background:${CHIP_COLORS[v] || '#555'}"></div>`
        ).join('');
    }

    function updateUI() {
        document.getElementById('balance').textContent = fmt(balance);
        document.getElementById('totalBet').textContent = fmt(totalBet());
        const sp = document.getElementById('sessionProfit');
        sp.textContent = (sessionProfit >= 0 ? '+' : '') + fmt(sessionProfit);
        sp.className = 'value ' + (sessionProfit > 0 ? 'win-value' : sessionProfit < 0 ? 'loss-value' : '');

        document.getElementById('betPlayerAmount').textContent = bets.player ? fmt(bets.player) : '';
        document.getElementById('betTieAmount').textContent = bets.tie ? fmt(bets.tie) : '';
        document.getElementById('betBankerAmount').textContent = bets.banker ? fmt(bets.banker) : '';

        document.getElementById('stackPlayer').innerHTML = renderChipStack(betChipHistory.player);
        document.getElementById('stackTie').innerHTML = renderChipStack(betChipHistory.tie);
        document.getElementById('stackBanker').innerHTML = renderChipStack(betChipHistory.banker);

        document.getElementById('btnDeal').disabled = totalBet() === 0;
        ['betPlayer','betTie','betBanker'].forEach(id => document.getElementById(id).classList.remove('selected'));
        if (bets.player) document.getElementById('betPlayer').classList.add('selected');
        if (bets.tie) document.getElementById('betTie').classList.add('selected');
        if (bets.banker) document.getElementById('betBanker').classList.add('selected');
    }

    function spawnWinParticles(color) {
        const burst = document.getElementById('winBurst');
        burst.innerHTML = '';
        for (let i = 0; i < 24; i++) {
            const angle = (Math.PI * 2 * i) / 24;
            const dist = 60 + Math.random() * 80;
            const spark = document.createElement('div');
            spark.className = 'win-spark';
            spark.style.cssText = `
                background: ${color};
                --tx: ${Math.cos(angle) * dist}px;
                --ty: ${Math.sin(angle) * dist}px;
                box-shadow: 0 0 6px ${color};
            `;
            burst.appendChild(spark);
        }
    }

    async function deal() {
        if (isDealing || totalBet() === 0) return;
        isDealing = true;
        lastBets = { ...bets };
        lastBetChipHistory = {
            player: [...betChipHistory.player],
            banker: [...betChipHistory.banker],
            tie: [...betChipHistory.tie]
        };

        document.getElementById('btnDeal').style.display = 'none';
        document.getElementById('btnClear').style.display = 'none';
        document.getElementById('btnRebet').style.display = 'none';
        document.querySelectorAll('.bet-spot').forEach(s => s.classList.add('disabled'));
        document.querySelectorAll('.chip').forEach(c => c.style.pointerEvents = 'none');

        document.getElementById('statusMessage').textContent = 'Dealing...';
        document.getElementById('playerCards').innerHTML = '';
        document.getElementById('bankerCards').innerHTML = '';
        document.getElementById('playerScore').style.visibility = 'hidden';
        document.getElementById('bankerScore').style.visibility = 'hidden';
        document.getElementById('playerScore').classList.remove('winner');
        document.getElementById('bankerScore').classList.remove('winner');
        document.getElementById('playerNatural').textContent = '';
        document.getElementById('bankerNatural').textContent = '';

        const playerHand = [];
        const bankerHand = [];

        playerHand.push(drawCard());
        await addCardToDOM('playerCards', playerHand[0], 300);
        bankerHand.push(drawCard());
        await addCardToDOM('bankerCards', bankerHand[0], 300);
        playerHand.push(drawCard());
        await addCardToDOM('playerCards', playerHand[1], 300);
        bankerHand.push(drawCard());
        await addCardToDOM('bankerCards', bankerHand[1], 300);

        let pTotal = handTotal(playerHand);
        let bTotal = handTotal(bankerHand);

        showScore('playerScore', pTotal);
        showScore('bankerScore', bTotal);

        const pNatural = pTotal >= 8;
        const bNatural = bTotal >= 8;

        if (pNatural) document.getElementById('playerNatural').textContent = 'Natural ' + pTotal;
        if (bNatural) document.getElementById('bankerNatural').textContent = 'Natural ' + bTotal;

        let playerDrew = null;

        if (!pNatural && !bNatural) {
            if (pTotal <= 5) {
                await sleep(300);
                document.getElementById('statusMessage').textContent = 'Player draws...';
                const c = drawCard();
                playerHand.push(c);
                playerDrew = c.value;
                await addCardToDOM('playerCards', c, 400);
                pTotal = handTotal(playerHand);
                showScore('playerScore', pTotal);
            }

            let bankerDraws = false;
            if (playerDrew === null) {
                bankerDraws = bTotal <= 5;
            } else {
                if (bTotal <= 2) bankerDraws = true;
                else if (bTotal === 3) bankerDraws = playerDrew !== 8;
                else if (bTotal === 4) bankerDraws = playerDrew >= 2 && playerDrew <= 7;
                else if (bTotal === 5) bankerDraws = playerDrew >= 4 && playerDrew <= 7;
                else if (bTotal === 6) bankerDraws = playerDrew === 6 || playerDrew === 7;
            }

            if (bankerDraws) {
                await sleep(300);
                document.getElementById('statusMessage').textContent = 'Banker draws...';
                const c = drawCard();
                bankerHand.push(c);
                await addCardToDOM('bankerCards', c, 400);
                bTotal = handTotal(bankerHand);
                showScore('bankerScore', bTotal);
            }
        }

        await sleep(500);

        let winner, resultClass, resultText;
        if (pTotal > bTotal) {
            winner = 'player';
            resultClass = 'player-wins';
            resultText = 'Player Wins!';
            document.getElementById('playerScore').classList.add('winner');
        } else if (bTotal > pTotal) {
            winner = 'banker';
            resultClass = 'banker-wins';
            resultText = 'Banker Wins!';
            document.getElementById('bankerScore').classList.add('winner');
        } else {
            winner = 'tie';
            resultClass = 'tie-wins';
            resultText = 'Tie!';
        }

        let payout = 0;
        if (winner === 'player') payout += bets.player * 2;
        if (winner === 'banker') payout += bets.banker * 2 * 0.95;
        if (winner === 'tie') {
            payout += bets.tie * 9;
            payout += bets.player + bets.banker;
        }
        payout = Math.floor(payout);

        const profit = payout - totalBet();
        sessionProfit += profit;
        balance += payout;

        const sparkColor = winner === 'player' ? '#60a5fa' : winner === 'banker' ? '#f87171' : '#4ade80';
        spawnWinParticles(sparkColor);

        const banner = document.getElementById('resultBanner');
        banner.className = 'result-banner ' + resultClass;
        const profitHtml = profit > 0
            ? `<div class="result-sub">+${fmt(profit)} chips</div>`
            : profit < 0 ? `<div class="result-sub">${fmt(profit)} chips</div>` : '';
        banner.innerHTML = resultText + profitHtml;
        setTimeout(() => banner.classList.add('show'), 50);

        document.getElementById('statusMessage').innerHTML =
            `${resultText} <span style="opacity:0.5">${pTotal} – ${bTotal}</span>`;

        stats.hands++;
        stats[winner]++;
        beadResults.push(winner);
        bigRoadData.push(winner);
        history.unshift({ round: roundNumber, winner, pTotal, bTotal, bet: totalBet(), profit });
        roundNumber++;

        updateStats();
        updateBeadRoad();
        updateBigRoad();
        updateHistory();

        bets = { player: 0, banker: 0, tie: 0 };
        betChipHistory = { player: [], banker: [], tie: [] };
        updateUI();

        setTimeout(() => banner.classList.remove('show'), 2200);

        document.getElementById('btnNewRound').style.display = '';
        isDealing = false;
    }
    window.deal = deal;

    function newRound() {
        document.getElementById('btnNewRound').style.display = 'none';
        document.getElementById('btnDeal').style.display = '';
        document.getElementById('btnClear').style.display = '';
        if (lastBets && (lastBets.player + lastBets.banker + lastBets.tie) > 0) {
            document.getElementById('btnRebet').style.display = '';
        }
        document.querySelectorAll('.bet-spot').forEach(s => s.classList.remove('disabled'));
        document.querySelectorAll('.chip').forEach(c => c.style.pointerEvents = '');
        document.getElementById('playerCards').innerHTML = '';
        document.getElementById('bankerCards').innerHTML = '';
        document.getElementById('playerScore').style.visibility = 'hidden';
        document.getElementById('bankerScore').style.visibility = 'hidden';
        document.getElementById('playerScore').classList.remove('winner');
        document.getElementById('bankerScore').classList.remove('winner');
        document.getElementById('playerNatural').textContent = '';
        document.getElementById('bankerNatural').textContent = '';
        document.getElementById('roundLabel').textContent = 'Round ' + roundNumber;
        document.getElementById('statusMessage').textContent = 'Place your bets';
        document.getElementById('winBurst').innerHTML = '';
        updateUI();
    }
    window.newRound = newRound;

    function showScore(id, score) {
        const el = document.getElementById(id);
        el.textContent = score;
        el.style.visibility = 'visible';
    }

    async function addCardToDOM(containerId, card, delay) {
        await sleep(delay || 200);
        const container = document.getElementById(containerId);
        container.insertAdjacentHTML('beforeend', renderCard(card));
    }

    function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

    function updateStats() {
        const total = stats.hands || 1;
        document.getElementById('statHands').textContent = stats.hands;
        document.getElementById('statPlayer').textContent = stats.player;
        document.getElementById('statBanker').textContent = stats.banker;
        document.getElementById('statTie').textContent = stats.tie;
        document.getElementById('barPlayer').style.width = (stats.player / total * 100) + '%';
        document.getElementById('barBanker').style.width = (stats.banker / total * 100) + '%';
        document.getElementById('barTie').style.width = (stats.tie / total * 100) + '%';
        const nonTie = stats.player + stats.banker || 1;
        document.getElementById('statPlayerPct').textContent = Math.round(stats.player / nonTie * 100) + '%';
        document.getElementById('statBankerPct').textContent = Math.round(stats.banker / nonTie * 100) + '%';
    }

    function updateBeadRoad() {
        const road = document.getElementById('beadRoad');
        const cells = 72;
        const start = Math.max(0, beadResults.length - cells);
        const visible = beadResults.slice(start);
        let html = '';
        for (let i = 0; i < cells; i++) {
            const isLatest = i === visible.length - 1;
            if (i < visible.length) {
                html += `<div class="bead ${visible[i]}${isLatest ? ' latest' : ''}"></div>`;
            } else {
                html += '<div class="bead empty"></div>';
            }
        }
        road.innerHTML = html;
    }

    function updateBigRoad() {
        const cols = 16, rows = 6;
        const grid = Array.from({ length: rows }, () => Array(cols).fill(null));

        let col = 0, row = 0, lastWinner = null;
        for (const result of bigRoadData) {
            if (result === 'tie') {
                if (lastWinner) {
                    const r = Math.max(0, row - (lastWinner === result ? 0 : 0));
                    const c = col;
                    if (grid[r] && grid[r][c]) {
                        grid[r][c] = grid[r][c] + '+tie';
                    }
                }
                continue;
            }
            if (result === lastWinner) {
                row++;
                if (row >= rows) { row = rows - 1; col++; }
            } else {
                if (lastWinner !== null) col++;
                row = 0;
            }
            if (col >= cols) break;
            grid[row][col] = result;
            lastWinner = result;
        }

        const el = document.getElementById('bigRoad');
        let html = '';
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = grid[r][c];
                if (!cell) {
                    html += '<div class="big-road-cell empty"></div>';
                } else {
                    const base = cell.replace('+tie', '');
                    const hasTie = cell.includes('+tie');
                    html += `<div class="big-road-cell ${base}${hasTie ? ' tie-mark' : ''}"></div>`;
                }
            }
        }
        el.innerHTML = html;
    }

    function updateHistory() {
        const list = document.getElementById('historyList');
        const recent = history.slice(0, 8);
        list.innerHTML = recent.map(h => {
            const resultColor = h.winner === 'player' ? 'stat-player' : h.winner === 'banker' ? 'stat-banker' : 'stat-tie';
            const resultLabel = h.winner.charAt(0).toUpperCase() + h.winner.slice(1);
            const payoutClass = h.profit > 0 ? 'win' : h.profit < 0 ? 'loss' : '';
            const payoutText = h.profit > 0 ? '+' + fmt(h.profit) : fmt(h.profit);
            return `<div class="history-entry">
                <span class="history-round">#${h.round}</span>
                <span class="history-result ${resultColor}">${resultLabel} ${h.pTotal}–${h.bTotal}</span>
                <span class="history-payout ${payoutClass}">${payoutText}</span>
            </div>`;
        }).join('');
    }

    // Ambient particles
    function initParticles() {
        const container = document.getElementById('particles');
        for (let i = 0; i < 12; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.left = Math.random() * 100 + '%';
            p.style.bottom = '-5px';
            p.style.animationDuration = (4 + Math.random() * 6) + 's';
            p.style.animationDelay = (Math.random() * 8) + 's';
            p.style.width = (1 + Math.random() * 2) + 'px';
            p.style.height = p.style.width;
            container.appendChild(p);
        }
    }

    // Init
    createShoe();
    updateBeadRoad();
    updateBigRoad();
    initParticles();
    updateUI();
})();
</script>
@endpush
