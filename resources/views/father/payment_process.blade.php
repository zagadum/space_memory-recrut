@extends('student.layout.theme.master_student')

@section('styles')
<style>
:root {
    --teal:      #26F9FF;
    --teal-dim:  rgba(38,249,255,0.10);
    --teal-glow: rgba(38,249,255,0.22);
    --green:     #4ade80;
    --green-dim: rgba(74,222,128,0.10);
    --yellow:    #fbbf24;
    --bg:        #04151d;
    --surface-1: #0d2535;
    --surface-2: #112d40;
    --border:    rgba(38,249,255,0.12);
    --text:      #f2f2f2;
    --muted:     rgba(242,242,242,0.45);
    --r-lg:      20px;
    --r-md:      12px;
}

body { background: var(--bg) !important; }
.content-area { background: var(--bg) !important; padding: 0 !important; }
header.d-lg-none { background: var(--bg) !important; border-bottom: 1px solid var(--border); }

/* ── WRAP ── */
.pay-wrap {
    min-height: 100vh;
    padding: 44px 52px 80px;
    background: var(--bg);
    color: var(--text);
    font-family: 'Roboto', sans-serif;
    position: relative;
}
.pay-wrap::before {
    content: '';
    position: fixed; top: -120px; right: -80px;
    width: 520px; height: 520px;
    background: radial-gradient(circle, rgba(38,249,255,0.055) 0%, transparent 65%);
    pointer-events: none; z-index: 0;
}
.pay-wrap > * { position: relative; z-index: 1; }

/* ── PAGE HEADER ── */
.pay-head {
    display: flex; align-items: center; gap: 16px;
    margin-bottom: 36px;
}
.pay-head__icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: var(--teal-dim);
    border: 1px solid var(--teal-glow);
    display: flex; align-items: center; justify-content: center;
    color: var(--teal); font-size: 20px; flex-shrink: 0;
}
.pay-head h1 { font-size: 26px; font-weight: 700; margin: 0; color: #fff; letter-spacing: -0.3px; }
.pay-head p  { font-size: 13px; color: var(--muted); margin: 3px 0 0; }

/* ── LAYOUT ── */
.pay-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
    align-items: start;
}

/* ══════════════════════════════
   PAYMENT CARD (main)
══════════════════════════════ */
.pay-card {
    background: var(--surface-1);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    overflow: hidden;
}
.pay-card__header {
    padding: 24px 28px 0;
}
.pay-card__title {
    font-size: 16px; font-weight: 700; color: #fff; margin: 0 0 4px;
}
.pay-card__sub {
    font-size: 13px; color: var(--muted); margin: 0 0 24px;
}

/* ── PERIOD OPTIONS ── */
.period-list {
    display: flex; flex-direction: column; gap: 10px;
    padding: 0 28px 24px;
}

.period-item {
    position: relative;
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    padding: 16px 20px;
    cursor: pointer;
    transition: border-color .2s, background .2s, box-shadow .2s;
    background: transparent;
    display: flex; align-items: center; gap: 16px;
    user-select: none;
}
.period-item:hover {
    border-color: rgba(38,249,255,0.28);
    background: rgba(38,249,255,0.03);
}
.period-item.selected {
    border-color: var(--teal);
    background: rgba(38,249,255,0.06);
    box-shadow: 0 0 0 3px rgba(38,249,255,0.08);
}

/* Radio circle */
.period-item__radio {
    width: 20px; height: 20px;
    border-radius: 50%;
    border: 2px solid var(--border);
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    transition: border-color .2s;
}
.period-item.selected .period-item__radio {
    border-color: var(--teal);
}
.period-item__radio::after {
    content: '';
    width: 10px; height: 10px;
    border-radius: 50%;
    background: var(--teal);
    transform: scale(0);
    transition: transform .15s;
}
.period-item.selected .period-item__radio::after { transform: scale(1); }

/* Text */
.period-item__body { flex: 1; }
.period-item__name {
    font-size: 15px; font-weight: 600; color: #fff; margin: 0 0 3px;
}
.period-item__lessons {
    font-size: 12px; color: var(--muted);
}

/* Savings badge */
.period-item__save {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 12px; font-weight: 700;
    background: var(--green-dim);
    border: 1px solid rgba(74,222,128,0.25);
    color: var(--green);
    flex-shrink: 0;
}

/* Popular badge */
.period-item__popular {
    position: absolute; top: -1px; right: 16px;
    background: linear-gradient(135deg, #26F9FF, #179599);
    color: #04151d; font-size: 10px; font-weight: 800;
    padding: 3px 10px; border-radius: 0 0 8px 8px;
    letter-spacing: 0.5px; text-transform: uppercase;
}

/* ── DIVIDER ── */
.pay-card__divider {
    height: 1px; background: var(--border); margin: 0;
}

/* ── PRICE + BUTTON ROW ── */
.pay-card__footer {
    padding: 20px 28px;
    display: flex; align-items: center; justify-content: space-between; gap: 20px;
}
.pay-price { display: flex; flex-direction: column; gap: 2px; }
.pay-price__label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.6px; }
.pay-price__row { display: flex; align-items: baseline; gap: 10px; }
.pay-price__old {
    font-size: 16px; color: var(--muted);
    text-decoration: line-through;
}
.pay-price__new {
    font-size: 28px; font-weight: 800; color: #fff; letter-spacing: -0.5px;
}
.pay-price__discount {
    font-size: 11px; font-weight: 700;
    color: var(--green);
    background: var(--green-dim);
    border: 1px solid rgba(74,222,128,0.2);
    padding: 2px 8px; border-radius: 20px;
}

.pay-btn-wrap { position: relative; flex-shrink: 0; }

.pay-btn {
    display: inline-flex; align-items: center; gap: 9px;
    padding: 14px 32px;
    background: linear-gradient(135deg, #26F9FF, #179599);
    border: none; border-radius: 12px;
    color: #04151d; font-size: 15px; font-weight: 800;
    cursor: pointer; white-space: nowrap;
    box-shadow: 0 4px 18px rgba(38,249,255,0.22);
    transition: opacity .2s, transform .15s, box-shadow .2s;
}
.pay-btn:hover {
    opacity: .88; transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(38,249,255,0.35);
}

/* Заблокированное состояние */
.pay-btn.locked {
    background: var(--surface-2);
    color: var(--muted);
    box-shadow: none;
    cursor: not-allowed;
    border: 1px solid var(--border);
    opacity: 1; transform: none !important;
}
.pay-btn.locked:hover { opacity: 1; transform: none; box-shadow: none; }

/* Тултип */
.pay-btn-tooltip {
    display: none;
    position: absolute;
    bottom: calc(100% + 10px);
    right: 0;
    width: 240px;
    background: #1a3448;
    border: 1px solid rgba(251,191,36,0.3);
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 12.5px; color: var(--text); line-height: 1.5;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    z-index: 10;
    pointer-events: none;
}
.pay-btn-tooltip::after {
    content: '';
    position: absolute; bottom: -6px; right: 28px;
    width: 10px; height: 10px;
    background: #1a3448;
    border-right: 1px solid rgba(251,191,36,0.3);
    border-bottom: 1px solid rgba(251,191,36,0.3);
    transform: rotate(45deg);
}
.pay-btn-tooltip i { color: var(--yellow); margin-right: 5px; }
.pay-btn-wrap:hover .pay-btn-tooltip { display: block; }

/* ══════════════════════════════
   HISTORY CARD (right column)
══════════════════════════════ */
.hist-card {
    background: var(--surface-1);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    overflow: hidden;
}
.hist-card__title {
    font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.8px;
    color: var(--muted);
    padding: 18px 20px 14px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 8px;
}
.hist-card__title i { color: var(--teal); }

.hist-empty {
    padding: 28px 20px;
    text-align: center;
    font-size: 13px; color: var(--muted);
}
.hist-empty i { font-size: 28px; display: block; margin-bottom: 10px; opacity: .3; }

/* Payment history row */
.hist-row {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid rgba(38,249,255,0.06);
}
.hist-row:last-child { border-bottom: none; }
.hist-row__icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: var(--green-dim);
    border: 1px solid rgba(74,222,128,0.2);
    display: flex; align-items: center; justify-content: center;
    color: var(--green); font-size: 14px; flex-shrink: 0;
}
.hist-row__icon--pending {
    background: rgba(251,191,36,0.08);
    border-color: rgba(251,191,36,0.2);
    color: var(--yellow);
}
.hist-row__info { flex: 1; min-width: 0; }
.hist-row__period { font-size: 13px; font-weight: 600; color: #fff; }
.hist-row__date   { font-size: 11px; color: var(--muted); margin-top: 2px; }
.hist-row__amount { font-size: 14px; font-weight: 700; color: var(--green); flex-shrink: 0; }
.hist-row__amount--pending { color: var(--yellow); }

/* ══════════════════════════════
   MODAL
══════════════════════════════ */
.pay-modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(4,21,29,0.88);
    backdrop-filter: blur(6px);
    z-index: 1000;
    display: none; align-items: center; justify-content: center;
    padding: 20px;
}
.pay-modal-backdrop.active { display: flex; }

.pay-modal {
    background: var(--surface-1);
    border: 1px solid rgba(251,191,36,0.25);
    border-radius: 24px;
    padding: 36px 32px;
    width: 500px; max-width: 100%;
    position: relative;
    box-shadow: 0 24px 80px rgba(0,0,0,0.5);
    animation: modalIn .25s ease;
}
@keyframes modalIn {
    from { opacity: 0; transform: translateY(16px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.pay-modal__close {
    position: absolute; top: 16px; right: 16px;
    width: 32px; height: 32px; border-radius: 8px;
    border: 1px solid var(--border); background: transparent;
    color: var(--muted); font-size: 14px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s, color .2s;
}
.pay-modal__close:hover { background: var(--surface-2); color: #fff; }

.pay-modal__icon {
    width: 56px; height: 56px; border-radius: 16px;
    background: rgba(251,191,36,0.08);
    border: 1px solid rgba(251,191,36,0.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; margin-bottom: 20px;
}
.pay-modal h2 {
    font-size: 19px; font-weight: 700; color: #fff; margin: 0 0 16px;
}
.pay-modal__body {
    font-size: 13.5px; color: rgba(242,242,242,0.75);
    line-height: 1.75;
}
.pay-modal__body p { margin: 0 0 12px; }
.pay-modal__body p:last-child { margin-bottom: 0; }
.pay-modal__body strong { color: #fff; }

.pay-modal__actions {
    display: flex; gap: 10px; margin-top: 28px;
}
.pay-modal__btn-confirm {
    flex: 1; padding: 13px;
    background: linear-gradient(135deg, #26F9FF, #179599);
    border: none; border-radius: 11px;
    color: #04151d; font-size: 14px; font-weight: 800;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(38,249,255,0.2);
    transition: opacity .2s, transform .15s;
}
.pay-modal__btn-confirm:hover { opacity: .88; transform: translateY(-1px); }
.pay-modal__btn-cancel {
    padding: 13px 20px;
    background: transparent;
    border: 1px solid var(--border); border-radius: 11px;
    color: var(--muted); font-size: 14px; font-weight: 600;
    cursor: pointer;
    transition: background .2s, color .2s;
}
.pay-modal__btn-cancel:hover { background: var(--surface-2); color: #fff; }

/* ── BACK ── */
.pay-back {
    width: 38px; height: 38px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: transparent; color: var(--muted);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; cursor: pointer; text-decoration: none;
    transition: background .2s, color .2s, border-color .2s;
    flex-shrink: 0;
}
.pay-back:hover {
    background: var(--teal-dim);
    border-color: rgba(38,249,255,0.3);
    color: var(--teal); text-decoration: none;
}

/* ── RESPONSIVE ── */
@media (max-width: 991px) {
    .pay-wrap { padding: 24px 20px 110px; }
    .pay-grid { grid-template-columns: 1fr; }
    .pay-head h1 { font-size: 21px; }
    .pay-card__footer { flex-direction: column; align-items: stretch; }
    .pay-btn { justify-content: center; }
}
</style>
@endsection

@section('content')
@php
    $firstPlan = $paymentPlans->first();
@endphp
<div class="pay-wrap">

    @if($errors->any())
        <div style="margin-bottom:20px;padding:14px 18px;border-radius:12px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#fecaca;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="pay-head">
        <a href="{{ route('father.portal') }}" class="pay-back" title="{{ __('father.payment_process.back_to_portal') }}">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="pay-head__icon">
            <i class="fas fa-credit-card"></i>
        </div>
        <div>
            <h1>{{ __('father.payment_process.title') }}</h1>
            <p>{{ __('father.payment_process.subtitle') }}</p>
        </div>
    </div>

    <div class="pay-grid">

        {{-- ══════════════════════ LEFT ══════════════════════ --}}
        <div class="pay-card">
            <div class="pay-card__header">
                <div class="pay-card__title">{{ __('father.payment_process.select_period') }}</div>
                <div class="pay-card__sub">
                    {{ __('father.payment_process.student_label') }} <strong style="color:#fff">{{ ($student->name ?? '') . ' ' . ($student->surname ?? '') ?: __('father.payment_process.not_specified') }}</strong>
                    &nbsp;·&nbsp;
                    {{ __('father.payment_process.group_label') }} <strong style="color:var(--teal)">{{ $student->group?->name ?? __('father.payment_process.no_group') }}</strong>
                    &nbsp;·&nbsp;
                    {{ __('father.payment_process.project_label') }} <strong style="color:#fff">{{ $project->name ?? '—' }}</strong>
                </div>
            </div>

            <div class="period-list">

                @forelse($paymentPlans as $plan)
                    <div class="period-item {{ $loop->first ? 'selected' : '' }}"
                         data-plan-id="{{ $plan->id }}"
                         data-months="{{ $plan->months }}"
                         data-lessons="{{ $plan->lessons }}"
                         data-price="{{ number_format((float) $plan->price, 2, '.', '') }}"
                         data-old="{{ number_format((float) ($plan->old_price ?? $plan->price), 2, '.', '') }}"
                         data-currency="{{ $plan->currency }}">
                        @if($plan->is_featured)
                            <div class="period-item__popular">{{ __('father.payment_process.featured') }}</div>
                        @endif
                        <div class="period-item__radio"></div>
                        <div class="period-item__body">
                            <div class="period-item__name">{{ $plan->period_label }}</div>
                            <div class="period-item__lessons">{{ $plan->lessons }} {{ __('father.payment_process.lessons_count') }}</div>
                        </div>
                        @if(($plan->save_amount ?? 0) > 0)
                            <div class="period-item__save"><i class="fas fa-tag"></i> {{ __('father.payment_process.savings') }} {{ number_format((float) $plan->save_amount, 0) }} {{ $plan->currency }}</div>
                        @endif
                    </div>
                @empty
                    <div class="hist-empty">
                        <i class="fas fa-tags"></i>
                        {{ __('father.payment_process.no_plans') }}
                    </div>
                @endforelse

            </div>

            <div class="pay-card__divider"></div>

            <div class="pay-card__footer">
                <div class="pay-price">
                    <div class="pay-price__label">{{ __('father.payment_process.total_to_pay') }}</div>
                    <div class="pay-price__row">
                        <span class="pay-price__old" id="priceOld">
                            {{ $firstPlan ? number_format((float) ($firstPlan->old_price ?? $firstPlan->price), 0) . ' ' . $firstPlan->currency : '—' }}
                        </span>
                        <span class="pay-price__new" id="priceNew">
                            {{ $firstPlan ? number_format((float) $firstPlan->price, 0) . ' ' . $firstPlan->currency : '—' }}
                        </span>
                    </div>
                </div>
                <div class="pay-btn-wrap">
                    <button class="pay-btn {{ ($contract->signed ?? false) && $paymentPlans->isNotEmpty() ? '' : 'locked' }}" id="btnPay">
                        <i class="fas {{ ($contract->signed ?? false) && $paymentPlans->isNotEmpty() ? 'fa-credit-card' : 'fa-lock' }}"></i>
                        {{ __('father.payment_process.pay_btn') }}
                    </button>
                    @if(!($contract->signed ?? false))
                    <div class="pay-btn-tooltip">
                        <i class="fas fa-exclamation-triangle"></i>
                        {!! __('father.payment_process.sign_first') !!}
                    </div>
                    @elseif($paymentPlans->isEmpty())
                    <div class="pay-btn-tooltip">
                        <i class="fas fa-exclamation-triangle"></i>
                        {!! __('father.payment_process.no_plans_tooltip') !!}
                    </div>
                    @endif
                </div>

                <form id="paymentForm" action="{{ route('father.payment.create') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="payment_plan_id" id="formPaymentPlanId">
                </form>
            </div>
        </div>

        {{-- ══════════════════════ RIGHT — HISTORY ══════════════════════ --}}
        <div class="hist-card">
            <div class="hist-card__title">
                <i class="fas fa-history"></i>
                {{ __('father.payment_process.history_title') }}
            </div>

            @forelse($payments as $p)
            <div class="hist-row">
                <div class="hist-row__icon {{ $p->status === 'completed' ? '' : 'hist-row__icon--pending' }}">
                    <i class="fas {{ $p->status === 'completed' ? 'fa-check' : 'fa-clock' }}"></i>
                </div>
                <div class="hist-row__info">
                    <div class="hist-row__period">{{ $p->title ?? $p->period_label }}</div>
                    <div class="hist-row__date">{{ $p->created_at->format('d.m.Y H:i') }}</div>
                </div>
                <div class="hist-row__amount {{ $p->status === 'completed' ? '' : 'hist-row__amount--pending' }}">
                    {{ number_format($p->amount, 2) }} {{ $p->currency ?? 'PLN' }}
                </div>
            </div>
            @empty
            <div class="hist-empty">
                <i class="fas fa-receipt"></i>
                {{ __('father.payment_process.no_history') }}
            </div>
            @endforelse

        </div>

    </div>
</div>

{{-- ══════════════════════════════
     MODAL — важная информация
══════════════════════════════ --}}
<div class="pay-modal-backdrop" id="payModal">
    <div class="pay-modal">
        <button class="pay-modal__close" id="closeModal"><i class="fas fa-times"></i></button>

        <div class="pay-modal__icon">⚠️</div>
        <h2>{{ __('father.payment_process.modal_title') }}</h2>

        <div class="pay-modal__body">
            <p>{!! __('father.payment_process.modal_p1') !!}</p>
            <p>{!! __('father.payment_process.modal_p2') !!}</p>
            <p>{!! __('father.payment_process.modal_p3') !!}</p>
            <p>{!! __('father.payment_process.modal_p4') !!}</p>
            <p>{!! __('father.payment_process.modal_p5') !!}</p>
        </div>

        <div class="pay-modal__actions">
            <button class="pay-modal__btn-cancel" id="cancelModal">{{ __('father.payment_process.cancel_btn') }}</button>
            <button class="pay-modal__btn-confirm" id="confirmPay">
                <i class="fas fa-lock" style="margin-right:7px"></i>
                {{ __('father.payment_process.go_to_payment_btn') }}
            </button>
        </div>
    </div>
</div>
@endsection

@section('bottom-scripts')
<script>
$(document).ready(function() {
    console.log('Payment script loaded');

    const $priceOld = $('#priceOld');
    const $priceNew = $('#priceNew');
    let $selectedItem = $('.period-item.selected');

    function formatMoney(value, currency) {
        const number = parseFloat(value || 0);
        return (Math.round(number * 100) % 100 === 0 ? number.toFixed(0) : number.toFixed(2)) + ' ' + (currency || 'PLN');
    }

    function selectPeriod($item) {
        console.log('Selecting period:', $item.data('planId'));
        $('.period-item').removeClass('selected');
        $item.addClass('selected');
        $selectedItem = $item;
        const currency = $item.data('currency') || 'PLN';

        if ($priceOld.length && $item.data('old')) {
            $priceOld.text(formatMoney($item.data('old'), currency));
        }
        if ($priceNew.length && $item.data('price')) {
            $priceNew.text(formatMoney($item.data('price'), currency));
        }
    }

    $(document).on('click', '.period-item', function() {
        selectPeriod($(this));
    });

    const $btnPay = $('#btnPay');
    const $payModal = $('#payModal');

    if ($btnPay.length) {
        $btnPay.on('click', function(e) {
            e.preventDefault();
            console.log('Pay button clicked');
            if ($(this).hasClass('locked')) {
                console.log('Button is locked');
                return;
            }
            if ($payModal.length) {
                $payModal.addClass('active');
                console.log('Modal opened');
            } else {
                console.error('Modal #payModal not found');
            }
        });
    }

    function closeModal() {
        if ($payModal.length) $payModal.removeClass('active');
    }
    
    $('#closeModal, #cancelModal').on('click', closeModal);
    
    $payModal.on('click', function(e) {
        if (e.target === this) closeModal();
    });

    $('#confirmPay').on('click', function() {
        console.log('Confirm payment clicked');
        const $btn = $(this);
        $btn.html('<i class="fas fa-spinner fa-spin" style="margin-right:7px"></i>{{ __('father.payment_process.redirecting') }}');
        $btn.prop('disabled', true);

        const paymentPlanId = $selectedItem.length ? $selectedItem.data('planId') : null;

        console.log('Submitting form with:', {paymentPlanId});

        const $paymentPlanInp = $('#formPaymentPlanId');
        const $form       = $('#paymentForm');

        if ($form.length && $paymentPlanInp.length && paymentPlanId) {
            $paymentPlanInp.val(paymentPlanId);
            $form.submit();
        } else {
            console.error('Payment form or inputs not found');
            $btn.prop('disabled', false);
            $btn.html('{{ __('father.payment_process.form_error') }}');
        }
    });

});
</script>
@endsection
