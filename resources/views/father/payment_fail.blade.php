@extends('student.layout.theme.master_student')

@section('styles')
<style>
    .pf-wrapper {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
        padding: 0 16px;
    }

    /* ── CARD — same style as success page ── */
    .pf-card {
        width: 100%;
        max-width: 480px;
        background: #0f2536;
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 20px;
        padding: 40px 40px 36px;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow:
            0 2px 0 rgba(255,255,255,0.04) inset,
            0 24px 60px rgba(0, 0, 0, 0.45);
        animation: pfCardIn .5s ease forwards;
        z-index: 1;
    }
    @keyframes pfCardIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* subtle top border glow — warm instead of teal */
    .pf-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg,
            transparent 5%,
            rgba(251, 146, 60, 0.35) 35%,
            rgba(251, 146, 60, 0.35) 65%,
            transparent 95%);
    }

    /* ── ICON ── */
    .pf-icon-wrap {
        width: 68px; height: 68px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: rgba(251, 100, 80, 0.12);
        border: 1px solid rgba(251, 100, 80, 0.20);
        display: flex; align-items: center; justify-content: center;
        animation: pfIconIn .45s cubic-bezier(.34,1.4,.64,1) .2s both,
                   pfGentleShake .5s ease .7s both;
    }
    @keyframes pfIconIn {
        from { opacity: 0; transform: scale(.5); }
        to   { opacity: 1; transform: scale(1); }
    }
    @keyframes pfGentleShake {
        0%,100% { transform: translateX(0) rotate(0); }
        20%,60% { transform: translateX(-5px) rotate(-1.5deg); }
        40%,80% { transform: translateX( 5px) rotate( 1.5deg); }
    }

    .pf-icon-wrap svg {
        width: 30px; height: 30px;
        overflow: visible;
    }
    .pf-x-circle {
        fill: none; stroke: url(#pfXGrad); stroke-width: 2;
        stroke-dasharray: 95; stroke-dashoffset: 95;
        animation: pfDraw .5s ease .65s forwards;
    }
    .pf-x-line1, .pf-x-line2 {
        stroke: url(#pfXGrad); stroke-width: 2.5;
        stroke-linecap: round;
        stroke-dasharray: 18; stroke-dashoffset: 18;
    }
    .pf-x-line1 { animation: pfDraw .25s ease 1.15s forwards; }
    .pf-x-line2 { animation: pfDraw .25s ease 1.35s forwards; }
    @keyframes pfDraw { to { stroke-dashoffset: 0; } }

    /* ── BADGE ── */
    .pf-badge {
        display: inline-block;
        font-size: 10px; font-weight: 700;
        letter-spacing: 2px; text-transform: uppercase;
        color: rgba(251, 146, 60, 0.80);
        margin-bottom: 10px;
        animation: pfFadeUp .35s ease .3s both;
    }

    /* ── TITLE ── */
    .pf-title {
        font-size: 24px; font-weight: 700;
        color: #e8f0f5;
        margin: 0 0 10px;
        line-height: 1.3;
        animation: pfFadeUp .35s ease .38s both;
    }
    .pf-title span {
        color: #fb923c;
    }

    /* ── DESCRIPTION ── */
    .pf-description {
        font-size: 13.5px;
        color: rgba(200, 220, 235, 0.55);
        line-height: 1.65;
        margin-bottom: 22px;
        animation: pfFadeUp .35s ease .46s both;
    }
    .pf-description strong { color: rgba(200, 220, 235, 0.85); }

    @keyframes pfFadeUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── DETAIL BOX ── */
    .pf-detail-box {
        display: flex; align-items: flex-start; gap: 14px;
        background: rgba(251, 100, 80, 0.06);
        border: 1px solid rgba(251, 100, 80, 0.14);
        border-radius: 14px;
        padding: 16px 18px;
        margin-bottom: 26px;
        text-align: left;
        animation: pfFadeUp .35s ease .54s both;
    }
    .pf-detail-box__icon {
        flex-shrink: 0; margin-top: 1px;
        color: rgba(251, 146, 60, 0.70);
    }
    .pf-detail-box__icon svg { width: 17px; height: 17px; }
    .pf-detail-box__heading {
        font-size: 12px; font-weight: 600;
        color: rgba(251, 146, 60, 0.85);
        margin-bottom: 4px;
    }
    .pf-detail-box__msg {
        font-size: 12px;
        color: rgba(200, 220, 235, 0.45);
        line-height: 1.55;
    }

    /* ── BUTTONS ── */
    .pf-cta {
        display: flex; flex-direction: column;
        align-items: center; gap: 13px;
        animation: pfFadeUp .35s ease .62s both;
    }

    .pf-btn-primary {
        display: inline-flex; align-items: center; gap: 9px;
        padding: 13px 40px;
        background: linear-gradient(135deg, #fb923c 0%, #ef6820 100%);
        border: none; border-radius: 12px;
        color: #fff !important; font-size: 14px; font-weight: 700;
        cursor: pointer; text-decoration: none;
        box-shadow: 0 4px 18px rgba(251, 146, 60, 0.22);
        transition: transform .15s, box-shadow .2s, opacity .2s;
    }
    .pf-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 26px rgba(251, 146, 60, 0.35);
        opacity: .92; color: #fff !important; text-decoration: none;
    }
    .pf-btn-primary svg { width: 15px; height: 15px; }

    .pf-btn-secondary {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px;
        color: rgba(200, 220, 235, 0.40) !important;
        text-decoration: none; background: none; border: none;
        cursor: pointer;
        transition: color .2s;
    }
    .pf-btn-secondary:hover { color: rgba(200, 220, 235, 0.75) !important; }
    .pf-btn-secondary svg { width: 14px; height: 14px; }

    /* ── CONTACT ── */
    .pf-contact {
        margin-top: 22px;
        animation: pfFadeUp .35s ease .70s both;
    }
    .pf-contact__label {
        font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase;
        color: rgba(200, 220, 235, 0.22);
        margin-bottom: 12px;
    }
    .pf-contact__cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .pf-contact__card {
        display: flex; flex-direction: column;
        align-items: center; gap: 7px;
        padding: 13px 10px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        text-decoration: none;
        transition: background .2s, border-color .2s;
    }
    .pf-contact__card:hover {
        background: rgba(255, 255, 255, 0.055);
        border-color: rgba(255, 255, 255, 0.11);
    }
    .pf-contact__card-icon {
        width: 30px; height: 30px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
    }
    .pf-contact__card-icon--phone { background: rgba(74, 222, 128, 0.10); color: #4ade80; }
    .pf-contact__card-icon--email { background: rgba(96, 165, 250, 0.10); color: #60a5fa; }
    .pf-contact__card-icon svg { width: 14px; height: 14px; }
    .pf-contact__card-type {
        font-size: 10px; letter-spacing: 1px; text-transform: uppercase;
        color: rgba(200, 220, 235, 0.28);
    }
    .pf-contact__card-value {
        font-size: 11.5px; font-weight: 600;
        color: rgba(200, 220, 235, 0.55);
        word-break: break-all; text-align: center; line-height: 1.4;
    }

    /* ── MOBILE ── */
    @media (max-width: 520px) {
        .pf-card { padding: 28px 22px 24px; }
        .pf-title { font-size: 21px; }
        .pf-contact__cards { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="pf-wrapper">
    <div class="pf-card">
        <!-- Icon -->
        <div class="pf-icon-wrap">
            <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="pfXGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%"   stop-color="#fb923c"/>
                        <stop offset="100%" stop-color="#f87171"/>
                    </linearGradient>
                </defs>
                <circle class="pf-x-circle" cx="15" cy="15" r="12"/>
                <line   class="pf-x-line1"  x1="10" y1="10" x2="20" y2="20"/>
                <line   class="pf-x-line2"  x1="20" y1="10" x2="10" y2="20"/>
            </svg>
        </div>

        <!-- Badge + Title -->
        <div class="pf-badge">{{ __('father.payment_failed_badge') }}</div>
        <h1 class="pf-title">{{ __('father.payment_failed_title') }} <span>{{ __('father.payment_failed_title_highlight') }}</span></h1>

        <!-- Description -->
        <p class="pf-description">
            {{ __('father.payment_failed_desc') }}
            <strong>{{ __('father.payment_failed_desc_retry') }}</strong>
            {{ __('father.payment_failed_desc_support') }}
        </p>

        <!-- Detail box -->
        <div class="pf-detail-box">
            <div class="pf-detail-box__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8"  x2="12"    y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <div class="pf-detail-box__heading">{{ __('father.payment_failed_detail_heading') }}</div>
                <div class="pf-detail-box__msg">
                    {{ __('father.payment_failed_detail_msg') }}
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="pf-cta">
            <a href="{{ route('father.payment') }}" class="pf-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M23 4v6h-6"/>
                    <path d="M1 20v-6h6"/>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                </svg>
                {{ __('father.payment_failed_try_again') }}
            </a>
            <a href="{{ route('father.portal.index') }}" class="pf-btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                {{ __('father.payment_failed_back_dashboard') }}
            </a>
        </div>

        <!-- Contact -->
        <div class="pf-contact">
            <div class="pf-contact__label">{{ __('father.payment_failed_help') }}</div>
            <div class="pf-contact__cards">
                <a href="tel:+48573569807" class="pf-contact__card">
                    <div class="pf-contact__card-icon pf-contact__card-icon--phone">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.18h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.73a16 16 0 0 0 6.29 6.29l.92-.92a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7a2 2 0 0 1 1.72 2.02z"/>
                        </svg>
                    </div>
                    <span class="pf-contact__card-type">{{ __('father.payment_failed_phone_label') }}</span>
                    <span class="pf-contact__card-value">+48 573 569 807</span>
                </a>
                <a href="mailto:akademiaspacememory@gmail.com" class="pf-contact__card">
                    <div class="pf-contact__card-icon pf-contact__card-icon--email">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <span class="pf-contact__card-type">{{ __('father.payment_failed_email_label') }}</span>
                    <span class="pf-contact__card-value">akademiaspacememory@gmail.com</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@section('bottom-scripts')
{{-- Custom scripts for payment fail page can go here --}}
@endsection
