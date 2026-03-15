@extends('student.layout.theme.master_student')

@section('styles')
<style>
:root {
    --teal:      #26F9FF;
    --teal-dim:  rgba(38,249,255,0.10);
    --teal-glow: rgba(38,249,255,0.22);
    --gold:      #F5BE30;
    --gold-dim:  rgba(245,190,48,0.10);
    --green:     #4ade80;
    --green-dim: rgba(74,222,128,0.08);
    --bg:        #04151d;
    --surface-1: #0d2535;
    --surface-2: #112d40;
    --border:    rgba(38,249,255,0.12);
    --text:      #f2f2f2;
    --muted:     rgba(242,242,242,0.50);
    --r-lg:      20px;
    --r-md:      12px;
}
body { background: var(--bg) !important; }
.content-area { background: var(--bg) !important; padding: 0 !important; }
header.d-lg-none { background: var(--bg) !important; border-bottom: 1px solid var(--border); }

.lrn-wrap {
    min-height: 100vh;
    padding: 40px 52px 80px;
    background: var(--bg);
    color: var(--text);
    font-family: 'Roboto', sans-serif;
    position: relative;
    overflow: hidden;
}
.lrn-wrap::before {
    content: '';
    position: fixed; top: -120px; right: -80px;
    width: 520px; height: 520px;
    background: radial-gradient(circle, rgba(38,249,255,0.05) 0%, transparent 65%);
    pointer-events: none; z-index: 0;
}
.lrn-wrap::after {
    content: '';
    position: fixed; bottom: 60px; left: -100px;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(245,190,48,0.04) 0%, transparent 65%);
    pointer-events: none; z-index: 0;
}
.lrn-wrap > * { position: relative; z-index: 1; }

/* ── TOP BAR ── */
.lrn-topbar { display: flex; align-items: center; gap: 14px; margin-bottom: 36px; }
.lrn-back {
    width: 38px; height: 38px; border-radius: 10px;
    border: 1px solid var(--border);
    background: transparent; color: var(--muted);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; text-decoration: none;
    transition: background .2s, color .2s, border-color .2s; flex-shrink: 0;
}
.lrn-back:hover { background: var(--teal-dim); border-color: rgba(38,249,255,0.3); color: var(--teal); text-decoration: none; }
.lrn-topbar__icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: var(--gold-dim); border: 1px solid rgba(245,190,48,0.22);
    display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
}
.lrn-topbar__info h1 { font-size: 24px; font-weight: 700; margin: 0; color: #fff; }
.lrn-topbar__info p  { font-size: 13px; color: var(--muted); margin: 3px 0 0; }

/* ── HERO ── */
.lrn-hero {
    background: linear-gradient(135deg, #0d2535 0%, #0a1f2e 100%);
    border: 1px solid var(--border); border-radius: var(--r-lg);
    padding: 44px 52px; margin-bottom: 28px;
    position: relative; overflow: hidden;
}
.lrn-hero::before {
    content: ''; position: absolute; top: -40px; right: -40px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(245,190,48,0.08) 0%, transparent 65%);
    pointer-events: none;
}
.lrn-hero__emoji { font-size: 52px; margin-bottom: 16px; display: block; line-height: 1; }
.lrn-hero h2 { font-size: 28px; font-weight: 800; color: #fff; margin: 0 0 12px; letter-spacing: -0.4px; }
.lrn-hero p { font-size: 15px; color: rgba(242,242,242,0.72); margin: 0; line-height: 1.7; max-width: 620px; }
.lrn-hero__badges { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
.lrn-badge {
    display: inline-flex; align-items: center; gap: 7px; padding: 7px 16px;
    border-radius: 20px; font-size: 13px; font-weight: 600;
    background: var(--surface-2); border: 1px solid var(--border); color: rgba(242,242,242,0.8);
}
.lrn-badge i { color: var(--teal); font-size: 12px; }
.lrn-badge--gold { border-color: rgba(245,190,48,0.25); }
.lrn-badge--gold i { color: var(--gold); }

/* ── SECTION TITLE ── */
.lrn-section-title {
    font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
    color: var(--muted); margin: 0 0 16px;
    display: flex; align-items: center; gap: 10px;
}
.lrn-section-title::after { content: ''; flex: 1; height: 1px; background: linear-gradient(to right, var(--border), transparent); }
.lrn-section-title i { color: var(--teal); }

/* ── METHODS GRID ── */
.lrn-methods { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px; }
.lrn-method {
    background: var(--surface-1); border: 1px solid var(--border);
    border-radius: var(--r-lg); padding: 28px 24px;
    position: relative; overflow: hidden;
    transition: border-color .25s, box-shadow .25s;
}
.lrn-method:hover { border-color: rgba(38,249,255,0.28); box-shadow: 0 8px 32px rgba(0,0,0,0.35); }
.lrn-method__emoji { font-size: 38px; margin-bottom: 16px; display: block; }
.lrn-method__title { font-size: 17px; font-weight: 700; color: #fff; margin: 0 0 10px; }
.lrn-method__text { font-size: 13.5px; color: var(--muted); line-height: 1.65; margin: 0; }
.lrn-method__accent {
    position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
    border-radius: 0 0 var(--r-lg) var(--r-lg);
    background: linear-gradient(90deg, var(--teal), transparent);
}
.lrn-method--gold .lrn-method__accent { background: linear-gradient(90deg, var(--gold), transparent); }
.lrn-method--green .lrn-method__accent { background: linear-gradient(90deg, var(--green), transparent); }

/* ── TWO-COL ── */
.lrn-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }
.lrn-card {
    background: var(--surface-1); border: 1px solid var(--border);
    border-radius: var(--r-lg); padding: 28px;
}
.lrn-card__head { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
.lrn-card__head-icon {
    width: 46px; height: 46px; border-radius: 12px;
    background: var(--teal-dim); border: 1px solid rgba(38,249,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: var(--teal); flex-shrink: 0;
}
.lrn-card__head-icon--gold { background: var(--gold-dim); border-color: rgba(245,190,48,0.2); color: var(--gold); }
.lrn-card__head-title { font-size: 16px; font-weight: 700; color: #fff; margin: 0; }
.lrn-card__head-sub { font-size: 12px; color: var(--muted); margin: 3px 0 0; }
.lrn-tip-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
.lrn-tip-list li {
    display: flex; gap: 12px; align-items: flex-start;
    font-size: 13.5px; color: rgba(242,242,242,0.80); line-height: 1.55;
}
.lrn-tip-list li::before {
    content: ''; width: 7px; height: 7px; border-radius: 50%;
    background: var(--teal); opacity: 0.7; flex-shrink: 0; margin-top: 6px;
}

/* ── FAQ ── */
.lrn-faq { margin-bottom: 28px; }
.lrn-faq-item {
    background: var(--surface-1); border: 1px solid var(--border);
    border-radius: var(--r-md); margin-bottom: 8px; overflow: hidden;
    transition: border-color .2s;
}
.lrn-faq-item:hover { border-color: rgba(38,249,255,0.22); }
.lrn-faq-item.open { border-color: rgba(38,249,255,0.22); }
.lrn-faq-q {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; font-size: 14px; font-weight: 600; color: #fff;
    cursor: pointer; user-select: none; gap: 14px;
}
.lrn-faq-q i { color: var(--teal); font-size: 12px; transition: transform .25s; flex-shrink: 0; }
.lrn-faq-item.open .lrn-faq-q i { transform: rotate(180deg); }
.lrn-faq-a { display: none; padding: 0 20px 18px; font-size: 13.5px; color: var(--muted); line-height: 1.7; }
.lrn-faq-item.open .lrn-faq-a { display: block; }

/* ── CONTACT ── */
.lrn-contact {
    background: var(--surface-1); border: 1px solid var(--border);
    border-radius: var(--r-lg); padding: 32px 36px;
    display: flex; align-items: center; gap: 32px; flex-wrap: wrap;
}
.lrn-contact__emoji { font-size: 44px; flex-shrink: 0; }
.lrn-contact__body { flex: 1; min-width: 200px; }
.lrn-contact__body h3 { font-size: 18px; font-weight: 700; color: #fff; margin: 0 0 6px; }
.lrn-contact__body p  { font-size: 13.5px; color: var(--muted); margin: 0; line-height: 1.6; }
.lrn-contact__links { display: flex; flex-direction: column; gap: 10px; flex-shrink: 0; }
.lrn-contact__link {
    display: inline-flex; align-items: center; gap: 10px; padding: 10px 20px;
    border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none;
    transition: opacity .2s, transform .15s; white-space: nowrap;
}
.lrn-contact__link:hover { opacity: .85; transform: translateY(-1px); text-decoration: none; }
.lrn-contact__link--phone { background: var(--teal-dim); border: 1px solid rgba(38,249,255,0.25); color: var(--teal); }
.lrn-contact__link--email { background: var(--gold-dim); border: 1px solid rgba(245,190,48,0.25); color: var(--gold); }

/* ── RESPONSIVE ── */
@media (max-width: 991px) {
    .lrn-wrap { padding: 24px 20px 110px; }
    .lrn-methods { grid-template-columns: 1fr; }
    .lrn-two-col { grid-template-columns: 1fr; }
    .lrn-hero { padding: 28px 24px; }
    .lrn-hero h2 { font-size: 22px; }
    .lrn-contact { flex-direction: column; gap: 20px; }
    .lrn-contact__links { flex-direction: row; flex-wrap: wrap; }
}
</style>
@endsection

@section('content')
<div class="lrn-wrap">

    {{-- TOP BAR --}}
    <div class="lrn-topbar">
        <a href="{{ route('father.portal') }}" class="lrn-back" title="{{ __('father.learn_view.back_to_portal') }}">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="lrn-topbar__icon">🎓</div>
        <div class="lrn-topbar__info">
            <h1>{{ __('father.learn_view.title') }}</h1>
            <p>{{ __('father.learn_view.subtitle') }}</p>
        </div>
    </div>

    {{-- HERO --}}
    <div class="lrn-hero">
        <span class="lrn-hero__emoji">🧠</span>
        <h2>{{ __('father.learn_view.methodology_title') }}</h2>
        <p>
            {!! __('father.learn_view.methodology_text') !!}
        </p>
        <div class="lrn-hero__badges">
            <span class="lrn-badge"><i class="fas fa-calendar-alt"></i> {{ __('father.learn_view.program_duration') }}</span>
            <span class="lrn-badge"><i class="fas fa-users"></i> {{ __('father.learn_view.groups_size') }}</span>
            <span class="lrn-badge"><i class="fas fa-clock"></i> {{ __('father.learn_view.lessons_freq') }}</span>
            <span class="lrn-badge lrn-badge--gold"><i class="fas fa-star"></i> {{ __('father.learn_view.age_range') }}</span>
            <span class="lrn-badge lrn-badge--gold"><i class="fas fa-graduation-cap"></i> {{ __('father.learn_view.platform_link') }}</span>
        </div>
    </div>

    {{-- 3 МЕТОДА --}}
    <div class="lrn-section-title"><i class="fas fa-book-open"></i> {{ __('father.learn_view.directions_title') }}</div>
    <div class="lrn-methods">
        <div class="lrn-method">
            <span class="lrn-method__emoji">🔢</span>
            <div class="lrn-method__title">{{ __('father.learn_view.mental_arithmetic_title') }}</div>
            <p class="lrn-method__text">{{ __('father.learn_view.mental_arithmetic_text') }}</p>
            <div class="lrn-method__accent"></div>
        </div>
        <div class="lrn-method lrn-method--gold">
            <span class="lrn-method__emoji">📖</span>
            <div class="lrn-method__title">{{ __('father.learn_view.speed_reading_title') }}</div>
            <p class="lrn-method__text">{{ __('father.learn_view.speed_reading_text') }}</p>
            <div class="lrn-method__accent"></div>
        </div>
        <div class="lrn-method lrn-method--green">
            <span class="lrn-method__emoji">🧩</span>
            <div class="lrn-method__title">{{ __('father.learn_view.memory_techniques_title') }}</div>
            <p class="lrn-method__text">{{ __('father.learn_view.memory_techniques_text') }}</p>
            <div class="lrn-method__accent"></div>
        </div>
    </div>

    {{-- СОВЕТЫ --}}
    <div class="lrn-section-title"><i class="fas fa-hands-helping"></i> {{ __('father.learn_view.tips_title') }}</div>
    <div class="lrn-two-col">
        <div class="lrn-card">
            <div class="lrn-card__head">
                <div class="lrn-card__head-icon"><i class="fas fa-home"></i></div>
                <div>
                    <div class="lrn-card__head-title">{{ __('father.learn_view.home_support_title') }}</div>
                    <div class="lrn-card__head-sub">{{ __('father.learn_view.home_support_subtitle') }}</div>
                </div>
            </div>
            <ul class="lrn-tip-list">
                @foreach(__('father.learn_view.home_support_tips') as $tip)
                    <li>{!! $tip !!}</li>
                @endforeach
            </ul>
        </div>
        <div class="lrn-card">
            <div class="lrn-card__head">
                <div class="lrn-card__head-icon lrn-card__head-icon--gold"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="lrn-card__head-title">{{ __('father.learn_view.what_to_expect_title') }}</div>
                    <div class="lrn-card__head-sub">{{ __('father.learn_view.what_to_expect_subtitle') }}</div>
                </div>
            </div>
            <ul class="lrn-tip-list">
                @foreach(__('father.learn_view.what_to_expect_tips') as $tip)
                    <li>{!! $tip !!}</li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- ВАЖНО ЗНАТЬ --}}
    <div class="lrn-section-title"><i class="fas fa-info-circle"></i> {{ __('father.learn_view.important_to_know_title') }}</div>
    <div class="lrn-two-col">
        <div class="lrn-card">
            <div class="lrn-card__head">
                <div class="lrn-card__head-icon"><i class="fas fa-user-shield"></i></div>
                <div>
                    <div class="lrn-card__head-title">{{ __('father.learn_view.attendance_rules_title') }}</div>
                    <div class="lrn-card__head-sub">{{ __('father.learn_view.attendance_rules_subtitle') }}</div>
                </div>
            </div>
            <ul class="lrn-tip-list">
                @foreach(__('father.learn_view.attendance_rules_tips') as $tip)
                    <li>{!! $tip !!}</li>
                @endforeach
            </ul>
        </div>
        <div class="lrn-card">
            <div class="lrn-card__head">
                <div class="lrn-card__head-icon lrn-card__head-icon--gold"><i class="fas fa-laptop"></i></div>
                <div>
                    <div class="lrn-card__head-title">{{ __('father.learn_view.platform_info_title') }}</div>
                    <div class="lrn-card__head-sub">{{ __('father.learn_view.platform_info_subtitle') }}</div>
                </div>
            </div>
            <ul class="lrn-tip-list">
                @foreach(__('father.learn_view.platform_info_tips') as $tip)
                    <li>{!! $tip !!}</li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- FAQ --}}
    <div class="lrn-section-title"><i class="fas fa-question-circle"></i> {{ __('father.learn_view.faq_title') }}</div>
    <div class="lrn-faq">

        @foreach(__('father.learn_view.faq') as $item)
        <div class="lrn-faq-item">
            <div class="lrn-faq-q">{{ $item['q'] }}<i class="fas fa-chevron-down"></i></div>
            <div class="lrn-faq-a">{!! $item['a'] !!}</div>
        </div>
        @endforeach

    </div>

    {{-- КОНТАКТЫ --}}
    <div class="lrn-section-title"><i class="fas fa-headset"></i> {{ __('father.learn_view.support_title') }}</div>
    <div class="lrn-contact">
        <div class="lrn-contact__emoji">🤝</div>
        <div class="lrn-contact__body">
            <h3>{{ __('father.learn_view.support_subtitle') }}</h3>
            <p>
                {{ __('father.learn_view.support_text') }}<br>
                <span style="color:rgba(242,242,242,0.4); font-size:12px;">
                    {{ __('father.learn_view.company_address') }}
                </span>
            </p>
        </div>
        <div class="lrn-contact__links">
            <a href="tel:+48730536091" class="lrn-contact__link lrn-contact__link--phone">
                <i class="fas fa-phone-alt"></i>+48 730 536 091
            </a>
            <a href="mailto:dzialjakosci.indigo@gmail.com" class="lrn-contact__link lrn-contact__link--email">
                <i class="fas fa-envelope"></i>dzialjakosci.indigo@gmail.com
            </a>
        </div>
    </div>

</div>
@endsection

@section('bottom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.lrn-faq-q').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = this.closest('.lrn-faq-item');
            var isOpen = item.classList.contains('open');
            document.querySelectorAll('.lrn-faq-item.open').forEach(function (el) { el.classList.remove('open'); });
            if (!isOpen) item.classList.add('open');
        });
    });
});
</script>
@endsection
