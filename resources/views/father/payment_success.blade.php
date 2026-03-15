@extends('student.layout.theme.master_student')

@section('styles')
<style>
:root {
    --teal:      #26F9FF;
    --teal-dim:  rgba(38,249,255,0.10);
    --teal-glow: rgba(38,249,255,0.22);
    --green:     #4ade80;
    --green-dim: rgba(74,222,128,0.10);
    --gold:      #fbbf24;
    --bg:        #04151d;
    --surface-1: #0d2535;
    --surface-2: #112d40;
    --border:    rgba(38,249,255,0.12);
    --text:      #f2f2f2;
    --muted:     rgba(242,242,242,0.45);
}

body { background: var(--bg) !important; overflow: hidden; }
.content-area { background: var(--bg) !important; padding: 0 !important; }
header.d-lg-none { background: var(--bg) !important; border-bottom: 1px solid var(--border); }

/* ── FULL-SCREEN CENTER ── */
.success-wrap {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
    padding: 0 16px;
}

/* ── CARD ── */
.success-card {
    width: 100%;
    max-width: 520px;
    background: var(--surface-1);
    border: 1px solid rgba(38,249,255,0.2);
    border-radius: 28px;
    padding: 36px 40px 32px;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow:
        0 0 0 1px rgba(38,249,255,0.05),
        0 24px 80px rgba(0,0,0,0.55),
        0 0 60px rgba(38,249,255,0.07);

    opacity: 0;
    transform: translateY(28px) scale(.97);
    animation: cardIn .65s cubic-bezier(.22,1,.36,1) .15s forwards;
}
@keyframes cardIn {
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* top glow line */
.success-card::before {
    content: '';
    position: absolute; top: 0; left: 50%;
    transform: translateX(-50%);
    width: 180px; height: 2px;
    background: linear-gradient(90deg, transparent, var(--teal), transparent);
}

/* ── ICON ── */
.sc-icon {
    width: 72px; height: 72px;
    margin: 0 auto 18px;
    border-radius: 50%;
    background: conic-gradient(from 0deg, rgba(38,249,255,0.18), rgba(74,222,128,0.18), rgba(38,249,255,0.18));
    display: flex; align-items: center; justify-content: center;
    position: relative;
    animation: rotateSlow 8s linear infinite;
    opacity: 0;
    animation: rotateSlow 8s linear infinite, popIn .5s cubic-bezier(.34,1.56,.64,1) .55s forwards;
}
@keyframes rotateSlow { to { transform: rotate(360deg); } }
@keyframes popIn {
    from { opacity: 0; transform: scale(.4) rotate(0deg); }
    to   { opacity: 1; transform: scale(1) rotate(360deg); }
}
.sc-icon__inner {
    position: absolute; inset: 5px;
    background: var(--surface-1);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px;
}

/* ── TEXT ── */
.sc-badge {
    font-size: 10px; font-weight: 700; letter-spacing: 2px;
    text-transform: uppercase; color: var(--teal);
    margin-bottom: 8px;
    opacity: 0; animation: fadeUp .4s ease .75s forwards;
}
.sc-title {
    font-size: 26px; font-weight: 800; color: #fff;
    margin: 0 0 10px; line-height: 1.2; letter-spacing: -.4px;
    opacity: 0; animation: fadeUp .4s ease .85s forwards;
}
.sc-title span {
    background: linear-gradient(135deg, #26F9FF, #4ade80);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}
.sc-text {
    font-size: 13px; color: var(--muted); line-height: 1.65;
    margin-bottom: 20px;
    opacity: 0; animation: fadeUp .4s ease .95s forwards;
}
.sc-text strong { color: #fff; }

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── SUMMARY ROW ── */
.sc-summary {
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(38,249,255,0.05);
    border: 1px solid rgba(38,249,255,0.12);
    border-radius: 14px;
    padding: 14px 20px;
    margin-bottom: 14px;
    opacity: 0; animation: fadeUp .4s ease 1.05s forwards;
}
.sc-summary__left { text-align: left; }
.sc-summary__label { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: .6px; }
.sc-summary__val   { font-size: 15px; font-weight: 700; color: #fff; margin-top: 2px; }
.sc-summary__sub   { font-size: 11px; color: var(--muted); }
.sc-summary__price { font-size: 26px; font-weight: 800; color: var(--teal); letter-spacing: -.5px; }
.sc-summary__price small { font-size: 13px; font-weight: 500; color: var(--muted); }

/* ── PILLS ROW ── */
.sc-pills {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    gap: 8px; margin-bottom: 22px;
    opacity: 0; animation: fadeUp .4s ease 1.15s forwards;
}
.sc-pill {
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border);
    border-radius: 12px; padding: 12px 10px;
    font-size: 11.5px; color: var(--muted); line-height: 1.4;
    text-align: center;
}
.sc-pill i { display: block; font-size: 16px; margin-bottom: 6px; }
.sc-pill--teal i  { color: var(--teal); }
.sc-pill--green i { color: var(--green); }
.sc-pill--gold i  { color: var(--gold); }
.sc-pill strong   { color: #fff; display: block; font-size: 12px; margin-bottom: 2px; }

/* ── BUTTON ── */
.sc-cta {
    opacity: 0; animation: fadeUp .4s ease 1.25s forwards;
}
.sc-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 40px;
    background: linear-gradient(135deg, #26F9FF, #179599);
    border: none; border-radius: 12px;
    color: #04151d; font-size: 14px; font-weight: 800;
    cursor: pointer; text-decoration: none;
    box-shadow: 0 4px 18px rgba(38,249,255,0.22);
    transition: opacity .2s, transform .15s, box-shadow .2s;
}
.sc-btn:hover {
    opacity: .88; transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(38,249,255,0.36);
    color: #04151d; text-decoration: none;
}

/* ── CORNER GLOWS ── */
.sc-glow-tr {
    position: absolute; top: 0; right: 0;
    width: 120px; height: 120px;
    background: radial-gradient(circle at 100% 0%, rgba(74,222,128,0.06) 0%, transparent 65%);
    pointer-events: none;
}
.sc-glow-bl {
    position: absolute; bottom: 0; left: 0;
    width: 120px; height: 120px;
    background: radial-gradient(circle at 0% 100%, rgba(38,249,255,0.05) 0%, transparent 65%);
    pointer-events: none;
}

/* ── MOBILE ── */
@media (max-width: 540px) {
    .success-card { padding: 28px 22px 24px; }
    .sc-title { font-size: 22px; }
    .sc-pills { grid-template-columns: 1fr; gap: 6px; }
    .sc-pill { display: flex; align-items: center; gap: 10px; text-align: left; padding: 10px 14px; }
    .sc-pill i { display: inline; margin-bottom: 0; font-size: 14px; }
}
</style>
@endsection

@section('content')


<div class="success-wrap">
    <div class="success-card">
        <div class="sc-glow-tr"></div>
        <div class="sc-glow-bl"></div>

        {{-- Icon --}}
        <div class="sc-icon">
            <div class="sc-icon__inner">🎉</div>
        </div>

        {{-- Text --}}
        <div class="sc-badge">Оплата прошла успешно</div>
        <h1 class="sc-title">Благодарим за <span>оплату!</span></h1>
        <p class="sc-text">
            Мы <strong>забронировали место</strong> для вашего ребёнка в группе.
            Наши консультанты свяжутся с вами как только все родители подпишут договор и оплатят занятия —
            тогда состоится <strong>официальный старт обучения!</strong>
        </p>

        {{-- Summary --}}
        <div class="sc-summary">
            <div class="sc-summary__left">
                <div class="sc-summary__label">Оплачено</div>
                <div class="sc-summary__val">{{ $payment->title ?? $payment->period_label ?? 'Оплата обучения' }}</div>
                <div class="sc-summary__sub">{{ $payment->lessons ?? '4' }} занятия · {{ $student->group?->name ?? 'Группа A' }}</div>
            </div>
            <div class="sc-summary__price">
                {{ number_format($payment->amount ?? 440, 0) }}&nbsp;<small>{{ $payment->currency ?? 'PLN' }}</small>
            </div>
        </div>

        {{-- Pills --}}
        <div class="sc-pills">
            <div class="sc-pill sc-pill--teal">
                <i class="fas fa-map-marker-alt"></i>
                <strong>Место</strong>
                Забронировано в группе
            </div>
            <div class="sc-pill sc-pill--gold">
                <i class="fas fa-envelope"></i>
                <strong>E-mail</strong>
                Придёт в день 1-го занятия
            </div>
            <div class="sc-pill sc-pill--green">
                <i class="fas fa-sync-alt"></i>
                <strong>Перерасчёт</strong>
                Остаток → след. месяц
            </div>
        </div>

        {{-- CTA --}}
        <div class="sc-cta">
            <a href="{{ route('father.portal') }}" class="sc-btn">
                <i class="fas fa-home"></i>
                На главную
            </a>
        </div>
    </div>
</div>

@endsection

@section('bottom-scripts')
<script>
(function () {
    const canvas = document.createElement('canvas');
    canvas.id    = 'confettiCanvas';
    canvas.style.cssText = 'position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:9999;transition:opacity 1.5s ease;';
    document.body.appendChild(canvas);
    const ctx    = canvas.getContext('2d');
    let W, H;

    function resize() {
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    const COLORS = ['#26F9FF', '#4ade80', '#fbbf24', '#f472b6', '#a78bfa', '#fb923c', '#ffffff'];
    function rand(a, b) { return a + Math.random() * (b - a); }

    function Piece() {
        this.reset(true);
    }
    Piece.prototype.reset = function (fromTop) {
        this.x     = rand(0, W);
        this.y     = fromTop ? rand(-H, -10) : rand(-200, -10);
        this.w     = rand(6, 13);
        this.h     = rand(3, 7);
        this.color = COLORS[Math.floor(Math.random() * COLORS.length)];
        this.vx    = rand(-1.5, 1.5);
        this.vy    = rand(1.5, 4.5);
        this.rot   = rand(0, Math.PI * 2);
        this.rotV  = rand(-.07, .07);
        this.alpha = rand(.7, 1);
        this.shape = Math.random() > .5 ? 'rect' : 'circle';
        this.active = true;
    };
    Piece.prototype.update = function () {
        this.x   += this.vx;
        this.y   += this.vy;
        this.rot += this.rotV;
        if (this.y > H + 50) this.active = false;
    };
    Piece.prototype.draw = function () {
        if (!this.active) return;
        ctx.save();
        ctx.globalAlpha = this.alpha;
        ctx.fillStyle   = this.color;
        ctx.translate(this.x, this.y);
        ctx.rotate(this.rot);
        if (this.shape === 'rect') {
            ctx.fillRect(-this.w / 2, -this.h / 2, this.w, this.h);
        } else {
            ctx.beginPath();
            ctx.ellipse(0, 0, this.w / 2, this.h / 2, 0, 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.restore();
    };

    const pieces = [];
    const COUNT  = 150;
    for (let i = 0; i < COUNT; i++) {
        const p = new Piece();
        p.y = rand(-H, H * 0.5); 
        pieces.push(p);
    }

    let running = true;
    function loop() {
        if (!running) return;
        ctx.clearRect(0, 0, W, H);
        let alive = false;
        pieces.forEach(p => {
            if (p.active) {
                p.update();
                p.draw();
                alive = true;
            }
        });
        
        if (!alive) {
            running = false;
            canvas.remove();
        } else {
            requestAnimationFrame(loop);
        }
    }
    loop();

    // Start fading out the whole canvas after 2.5 seconds
    setTimeout(() => {
        canvas.style.opacity = '0';
    }, 2500);

    // Completely remove after animation
    setTimeout(() => {
        running = false;
        if (canvas.parentNode) canvas.remove();
    }, 4500);
})();
</script>
@endsection
