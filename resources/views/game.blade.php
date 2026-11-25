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

    /* Table */
    .table-area {
        background: radial-gradient(ellipse at center, #1a3a2a 0%, #0f2318 70%, #0a1a10 100%);
        border: 3px solid #2d5a3d;
        border-radius: 1.5rem;
        padding: 2rem 1.5rem;
        box-shadow: inset 0 0 60px rgba(0,0,0,0.5), 0 8px 32px rgba(0,0,0,0.4);
        position: relative;
        overflow: hidden;
    }
    .table-area::before {
        content: '';
        position: absolute;
        inset: 8px;
        border: 1px solid rgba(255,215,0,0.15);
        border-radius: 1.25rem;
        pointer-events: none;
    }

    /* Status bar */
    .game-status {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .game-status .round { font-size: 0.75rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.1em; }
    .game-status .message {
        font-size: 1.125rem;
        font-weight: 700;
        color: #ffd700;
        min-height: 1.75rem;
    }

    /* Hands */
    .hands {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 1rem;
        align-items: start;
        margin-bottom: 2rem;
    }
    .hand { text-align: center; }
    .hand-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        margin-bottom: 0.75rem;
    }
    .hand-player .hand-label { color: #60a5fa; }
    .hand-banker .hand-label { color: #f87171; }
    .hand-score {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 1.25rem;
        font-weight: 800;
        margin-top: 0.75rem;
    }
    .hand-player .hand-score { background: rgba(96,165,250,0.2); color: #60a5fa; border: 2px solid rgba(96,165,250,0.4); }
    .hand-banker .hand-score { background: rgba(248,113,113,0.2); color: #f87171; border: 2px solid rgba(248,113,113,0.4); }
    .vs {
        font-size: 1rem;
        font-weight: 800;
        color: rgba(255,255,255,0.2);
        align-self: center;
        padding-top: 2rem;
    }

    /* Cards */
    .cards-row {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        min-height: 100px;
    }
    .card {
        width: 68px;
        height: 96px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.5s ease;
    }
    .card-front, .card-back {
        position: absolute;
        inset: 0;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        backface-visibility: hidden;
    }
    .card-front {
        background: #fff;
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    }
    .card-back {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255,255,255,0.05) 5px, rgba(255,255,255,0.05) 10px);
        border: 2px solid #60a5fa;
        transform: rotateY(180deg);
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    }
    .card.face-down { transform: rotateY(180deg); }
    .card.dealing {
        animation: dealIn 0.4s ease-out;
    }
    @keyframes dealIn {
        from { opacity: 0; transform: translateY(-40px) scale(0.7); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .card.dealing.face-down {
        animation: dealInFlipped 0.4s ease-out;
    }
    @keyframes dealInFlipped {
        from { opacity: 0; transform: translateY(-40px) scale(0.7) rotateY(180deg); }
        to { opacity: 1; transform: translateY(0) scale(1) rotateY(180deg); }
    }
    .card .rank { font-size: 1.25rem; line-height: 1; }
    .card .suit { font-size: 1.1rem; line-height: 1; }
    .card.red .rank, .card.red .suit { color: #dc2626; }
    .card.black .rank, .card.black .suit { color: #111; }
    .card-slot {
        width: 68px;
        height: 96px;
        border: 2px dashed rgba(255,255,255,0.1);
        border-radius: 8px;
    }

    /* Betting area */
    .betting-area {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .bet-spot {
        width: 140px;
        padding: 1rem 0.5rem;
        border-radius: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
        position: relative;
    }
    .bet-spot:hover { transform: translateY(-2px); }
    .bet-spot.selected { border-color: #ffd700; box-shadow: 0 0 20px rgba(255,215,0,0.3); }
    .bet-spot.disabled { opacity: 0.5; pointer-events: none; }
    .bet-player { background: rgba(96,165,250,0.15); }
    .bet-player.selected { background: rgba(96,165,250,0.3); }
    .bet-tie { background: rgba(34,197,94,0.15); }
    .bet-tie.selected { background: rgba(34,197,94,0.3); }
    .bet-banker { background: rgba(248,113,113,0.15); }
    .bet-banker.selected { background: rgba(248,113,113,0.3); }
    .bet-spot .label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.25rem;
    }
    .bet-player .label { color: #60a5fa; }
    .bet-tie .label { color: #22c55e; }
    .bet-banker .label { color: #f87171; }
    .bet-spot .payout { font-size: 0.65rem; color: rgba(255,255,255,0.4); }
    .bet-spot .amount {
        font-size: 1.25rem;
        font-weight: 800;
        color: #ffd700;
        margin-top: 0.25rem;
        min-height: 1.5rem;
    }

    /* Chips */
    .chips-row {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .chip {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 800;
        cursor: pointer;
        border: 3px dashed rgba(255,255,255,0.3);
        transition: all 0.15s;
        color: #fff;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }
    .chip:hover { transform: scale(1.15); }
    .chip.active { border-style: solid; border-color: #ffd700; transform: scale(1.15); box-shadow: 0 0 12px rgba(255,215,0,0.4); }
    .chip-1 { background: radial-gradient(circle, #666, #333); }
    .chip-5 { background: radial-gradient(circle, #ef4444, #991b1b); }
    .chip-10 { background: radial-gradient(circle, #3b82f6, #1e3a8a); }
    .chip-25 { background: radial-gradient(circle, #22c55e, #166534); }
    .chip-50 { background: radial-gradient(circle, #f59e0b, #92400e); }
    .chip-100 { background: radial-gradient(circle, #111, #000); border-color: rgba(255,215,0,0.5); }

    /* Controls */
    .controls {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .controls .btn { min-width: 100px; }
    .btn-deal { background: #ffd700; color: #111; font-weight: 800; }
    .btn-deal:hover { background: #fbbf24; color: #111; }
    .btn-deal:disabled { opacity: 0.4; cursor: not-allowed; }
    .btn-clear { background: rgba(255,255,255,0.1); color: #fff; }
    .btn-clear:hover { background: rgba(255,255,255,0.2); color: #fff; }
    .btn-rebet { background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
    .btn-rebet:hover { background: rgba(245,158,11,0.3); color: #f59e0b; }

    /* Balance */
    .balance-bar {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2rem;
        padding: 0.75rem;
        margin-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.1);
        font-size: 0.8rem;
    }
    .balance-bar .label { color: rgba(255,255,255,0.5); }
    .balance-bar .value { font-weight: 700; color: #ffd700; }
    .balance-bar .win-value { color: #22c55e; }
    .balance-bar .loss-value { color: #ef4444; }

    /* Result overlay */
    .result-banner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        padding: 1rem 3rem;
        border-radius: 1rem;
        font-size: 1.5rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        z-index: 10;
        pointer-events: none;
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .result-banner.show {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    .result-banner.player-wins { background: rgba(96,165,250,0.9); color: #fff; box-shadow: 0 0 40px rgba(96,165,250,0.5); }
    .result-banner.banker-wins { background: rgba(248,113,113,0.9); color: #fff; box-shadow: 0 0 40px rgba(248,113,113,0.5); }
    .result-banner.tie-wins { background: rgba(34,197,94,0.9); color: #fff; box-shadow: 0 0 40px rgba(34,197,94,0.5); }

    /* Sidebar */
    .sidebar { display: flex; flex-direction: column; gap: 1rem; }

    /* Scoreboard / Bead Road */
    .scoreboard {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 1rem;
        padding: 1rem;
    }
    .scoreboard h3 {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--color-text-muted);
        margin-bottom: 0.75rem;
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
        border: 1px solid rgba(255,255,255,0.1);
    }
    .bead.empty { background: rgba(255,255,255,0.03); }
    .bead.player { background: #3b82f6; border-color: #60a5fa; }
    .bead.banker { background: #ef4444; border-color: #f87171; }
    .bead.tie { background: #22c55e; border-color: #4ade80; }

    /* Stats */
    .stats-panel {
        background: var(--color-card);
        border: 1px solid var(--color-border);
        border-radius: 1rem;
        padding: 1rem;
    }
    .stats-panel h3 {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--color-text-muted);
        margin-bottom: 0.75rem;
    }
    .stat-row {
        display: flex;
        justify-content: space-between;
        padding: 0.375rem 0;
        font-size: 0.8rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
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
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--color-text-muted);
        margin-bottom: 0.75rem;
    }
    .history-entry {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.375rem 0;
        font-size: 0.75rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .history-entry:last-child { border-bottom: none; }
    .history-round { color: var(--color-text-muted); }
    .history-result { font-weight: 600; }
    .history-payout { font-weight: 600; }
    .history-payout.win { color: #22c55e; }
    .history-payout.loss { color: #ef4444; }

    /* Natural indicator */
    .natural {
        font-size: 0.6rem;
        font-weight: 700;
        color: #ffd700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="game-container">
    <div class="table-area" id="table">
        <div class="result-banner" id="resultBanner"></div>

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
            </div>
            <div class="bet-spot bet-tie" id="betTie" onclick="selectBetSpot('tie')">
                <div class="label">Tie</div>
                <div class="payout">Pays 8:1</div>
                <div class="amount" id="betTieAmount"></div>
            </div>
            <div class="bet-spot bet-banker" id="betBanker" onclick="selectBetSpot('banker')">
                <div class="label">Banker</div>
                <div class="payout">Pays 0.95:1</div>
                <div class="amount" id="betBankerAmount"></div>
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
            <div><span class="label">Balance: </span><span class="value" id="balance">1000</span></div>
            <div><span class="label">Total Bet: </span><span class="value" id="totalBet">0</span></div>
            <div><span class="label">Session: </span><span class="value" id="sessionProfit">0</span></div>
        </div>
    </div>

    <div class="sidebar">
        <div class="scoreboard">
            <h3>Bead Road</h3>
            <div class="bead-road" id="beadRoad"></div>
        </div>

        <div class="stats-panel">
            <h3>Statistics</h3>
            <div class="stat-row">
                <span class="stat-label">Hands Played</span>
                <span class="stat-value" id="statHands">0</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Player Wins</span>
                <span class="stat-value stat-player" id="statPlayer">0</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Banker Wins</span>
                <span class="stat-value stat-banker" id="statBanker">0</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Ties</span>
                <span class="stat-value stat-tie" id="statTie">0</span>
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
            <div id="historyList"><p style="color:var(--color-text-muted);font-size:0.75rem;">No hands played yet</p></div>
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

    let shoe = [];
    let balance = 1000;
    let bets = { player: 0, banker: 0, tie: 0 };
    let lastBets = null;
    let selectedChip = 10;
    let selectedSpot = null;
    let roundNumber = 1;
    let sessionProfit = 0;
    let stats = { hands: 0, player: 0, banker: 0, tie: 0 };
    let beadResults = [];
    let history = [];
    let isDealing = false;

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
    }

    function drawCard() {
        if (shoe.length < 20) createShoe();
        return shoe.pop();
    }

    function handTotal(cards) {
        return cards.reduce((s, c) => s + c.value, 0) % 10;
    }

    function isRed(suit) {
        return suit === '♥' || suit === '♦';
    }

    function renderCard(card, faceDown) {
        const color = isRed(card.suit) ? 'red' : 'black';
        const classes = `card ${color}${faceDown ? ' face-down' : ''} dealing`;
        return `<div class="${classes}">
            <div class="card-front"><span class="rank">${card.rank}</span><span class="suit">${card.suit}</span></div>
            <div class="card-back"></div>
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
        const amount = selectedChip;
        if (amount > balance) return;
        bets[spot] += amount;
        balance -= amount;
        updateUI();
    }
    window.selectBetSpot = selectBetSpot;

    function clearBets() {
        if (isDealing) return;
        balance += bets.player + bets.banker + bets.tie;
        bets = { player: 0, banker: 0, tie: 0 };
        updateUI();
    }
    window.clearBets = clearBets;

    function rebet() {
        if (isDealing || !lastBets) return;
        const total = lastBets.player + lastBets.banker + lastBets.tie;
        if (total > balance) return;
        bets = { ...lastBets };
        balance -= total;
        updateUI();
    }
    window.rebet = rebet;

    function totalBet() {
        return bets.player + bets.banker + bets.tie;
    }

    function updateUI() {
        document.getElementById('balance').textContent = balance;
        document.getElementById('totalBet').textContent = totalBet();
        const sp = document.getElementById('sessionProfit');
        sp.textContent = (sessionProfit >= 0 ? '+' : '') + sessionProfit;
        sp.className = 'value ' + (sessionProfit > 0 ? 'win-value' : sessionProfit < 0 ? 'loss-value' : '');
        document.getElementById('betPlayerAmount').textContent = bets.player || '';
        document.getElementById('betTieAmount').textContent = bets.tie || '';
        document.getElementById('betBankerAmount').textContent = bets.banker || '';
        document.getElementById('btnDeal').disabled = totalBet() === 0;
        ['betPlayer','betTie','betBanker'].forEach(id => {
            document.getElementById(id).classList.remove('selected');
        });
        if (bets.player) document.getElementById('betPlayer').classList.add('selected');
        if (bets.tie) document.getElementById('betTie').classList.add('selected');
        if (bets.banker) document.getElementById('betBanker').classList.add('selected');
    }

    async function deal() {
        if (isDealing || totalBet() === 0) return;
        isDealing = true;
        lastBets = { ...bets };

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
        document.getElementById('playerNatural').textContent = '';
        document.getElementById('bankerNatural').textContent = '';

        const playerHand = [];
        const bankerHand = [];

        // Deal initial 4 cards alternating: P, B, P, B
        playerHand.push(drawCard());
        await addCardToDOM('playerCards', playerHand[0], false, 300);
        bankerHand.push(drawCard());
        await addCardToDOM('bankerCards', bankerHand[0], false, 300);
        playerHand.push(drawCard());
        await addCardToDOM('playerCards', playerHand[1], false, 300);
        bankerHand.push(drawCard());
        await addCardToDOM('bankerCards', bankerHand[1], false, 300);

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
            // Player's third card rule
            if (pTotal <= 5) {
                const c = drawCard();
                playerHand.push(c);
                playerDrew = c.value;
                await addCardToDOM('playerCards', c, false, 500);
                pTotal = handTotal(playerHand);
                showScore('playerScore', pTotal);
            }

            // Banker's third card rule
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
                const c = drawCard();
                bankerHand.push(c);
                await addCardToDOM('bankerCards', c, false, 500);
                bTotal = handTotal(bankerHand);
                showScore('bankerScore', bTotal);
            }
        }

        await sleep(400);

        // Determine winner
        let winner, resultClass, resultText;
        if (pTotal > bTotal) {
            winner = 'player';
            resultClass = 'player-wins';
            resultText = 'Player Wins!';
        } else if (bTotal > pTotal) {
            winner = 'banker';
            resultClass = 'banker-wins';
            resultText = 'Banker Wins!';
        } else {
            winner = 'tie';
            resultClass = 'tie-wins';
            resultText = 'Tie!';
        }

        // Calculate payout
        let payout = 0;
        if (winner === 'player') payout += bets.player * 2;
        if (winner === 'banker') payout += bets.banker * 2 * 0.95; // 5% commission
        if (winner === 'tie') {
            payout += bets.tie * 9; // 8:1
            payout += bets.player + bets.banker; // push on tie
        }
        payout = Math.floor(payout);

        const profit = payout - totalBet();
        sessionProfit += profit;
        balance += payout;

        // Show result
        const banner = document.getElementById('resultBanner');
        banner.className = 'result-banner ' + resultClass;
        banner.textContent = resultText;
        setTimeout(() => banner.classList.add('show'), 50);

        const scoreText = `${pTotal} - ${bTotal}`;
        document.getElementById('statusMessage').innerHTML =
            `${resultText} <span style="opacity:0.6">(${scoreText})</span>` +
            (profit > 0 ? ` <span style="color:#22c55e">+${profit}</span>` : profit < 0 ? ` <span style="color:#ef4444">${profit}</span>` : '');

        // Update stats
        stats.hands++;
        stats[winner]++;
        beadResults.push(winner);
        history.unshift({ round: roundNumber, winner, pTotal, bTotal, bet: totalBet(), profit });
        roundNumber++;

        updateStats();
        updateBeadRoad();
        updateHistory();

        bets = { player: 0, banker: 0, tie: 0 };
        updateUI();

        setTimeout(() => {
            banner.classList.remove('show');
        }, 2000);

        // Show new round button
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
        document.getElementById('playerNatural').textContent = '';
        document.getElementById('bankerNatural').textContent = '';
        document.getElementById('roundLabel').textContent = 'Round ' + roundNumber;
        document.getElementById('statusMessage').textContent = 'Place your bets';
        updateUI();
    }
    window.newRound = newRound;

    function showScore(id, score) {
        const el = document.getElementById(id);
        el.textContent = score;
        el.style.visibility = 'visible';
    }

    async function addCardToDOM(containerId, card, faceDown, delay) {
        await sleep(delay || 200);
        const container = document.getElementById(containerId);
        container.insertAdjacentHTML('beforeend', renderCard(card, faceDown));
    }

    function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

    function updateStats() {
        document.getElementById('statHands').textContent = stats.hands;
        document.getElementById('statPlayer').textContent = stats.player;
        document.getElementById('statBanker').textContent = stats.banker;
        document.getElementById('statTie').textContent = stats.tie;
        const nonTie = stats.player + stats.banker || 1;
        document.getElementById('statPlayerPct').textContent = Math.round(stats.player / nonTie * 100) + '%';
        document.getElementById('statBankerPct').textContent = Math.round(stats.banker / nonTie * 100) + '%';
    }

    function updateBeadRoad() {
        const road = document.getElementById('beadRoad');
        const cells = 72; // 6 rows x 12 cols
        let html = '';
        for (let i = 0; i < cells; i++) {
            if (i < beadResults.length) {
                html += `<div class="bead ${beadResults[i]}"></div>`;
            } else {
                html += '<div class="bead empty"></div>';
            }
        }
        road.innerHTML = html;
    }

    function updateHistory() {
        const list = document.getElementById('historyList');
        const recent = history.slice(0, 10);
        list.innerHTML = recent.map(h => {
            const resultColor = h.winner === 'player' ? 'stat-player' : h.winner === 'banker' ? 'stat-banker' : 'stat-tie';
            const resultLabel = h.winner.charAt(0).toUpperCase() + h.winner.slice(1);
            const payoutClass = h.profit > 0 ? 'win' : h.profit < 0 ? 'loss' : '';
            const payoutText = h.profit > 0 ? '+' + h.profit : h.profit;
            return `<div class="history-entry">
                <span class="history-round">#${h.round}</span>
                <span class="history-result ${resultColor}">${resultLabel} ${h.pTotal}-${h.bTotal}</span>
                <span class="history-payout ${payoutClass}">${payoutText}</span>
            </div>`;
        }).join('');
    }

    // Init
    createShoe();
    updateBeadRoad();
    updateUI();
})();
</script>
@endpush
