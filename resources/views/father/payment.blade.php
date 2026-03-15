@extends('student.layout.theme.master_student')

@section('styles')
<style>
    :root {
        --teal: #26F9FF;
        --teal-dim: rgba(38, 249, 255, 0.10);
        --teal-glow: rgba(38, 249, 255, 0.22);
        --green: #4ade80;
        --green-dim: rgba(74, 222, 128, 0.10);
        --yellow: #fbbf24;
        --bg: #04151d;
        --surface-1: #0d2535;
        --surface-2: #112d40;
        --border: rgba(38, 249, 255, 0.12);
        --text: #f2f2f2;
        --muted: rgba(242, 242, 242, 0.45);
        --r-lg: 20px;
        --r-md: 12px;
    }

    body {
        background: var(--bg) !important;
    }

    .content-area {
        background: var(--bg) !important;
        padding: 0 !important;
    }

    header.d-lg-none {
        background: var(--bg) !important;
        border-bottom: 1px solid var(--border);
    }

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
        position: fixed;
        top: -120px;
        right: -80px;
        width: 520px;
        height: 520px;
        background: radial-gradient(circle, rgba(38, 249, 255, 0.055) 0%, transparent 65%);
        pointer-events: none;
        z-index: 0;
    }

    .pay-wrap>* {
        position: relative;
        z-index: 1;
    }

    .pay-head {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 36px;
    }

    .pay-head__icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: var(--teal-dim);
        border: 1px solid var(--teal-glow);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--teal);
        font-size: 20px;
        flex-shrink: 0;
    }

    .pay-head h1 {
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        color: #fff;
        letter-spacing: -0.3px;
    }

    .pay-head p {
        font-size: 13px;
        color: var(--muted);
        margin: 3px 0 0;
    }

    .pay-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        align-items: start;
    }

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
        font-size: 16px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 4px;
    }

    .pay-card__sub {
        font-size: 13px;
        color: var(--muted);
        margin: 0 0 24px;
    }

    .period-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
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
        display: flex;
        align-items: center;
        gap: 16px;
        user-select: none;
    }

    .period-item:hover {
        border-color: rgba(38, 249, 255, 0.28);
        background: rgba(38, 249, 255, 0.03);
    }

    .period-item.selected {
        border-color: var(--teal);
        background: rgba(38, 249, 255, 0.06);
        box-shadow: 0 0 0 3px rgba(38, 249, 255, 0.08);
    }

    .period-item__radio {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid var(--border);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: border-color .2s;
    }

    .period-item.selected .period-item__radio {
        border-color: var(--teal);
    }

    .period-item__radio::after {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--teal);
        transform: scale(0);
        transition: transform .15s;
    }

    .period-item.selected .period-item__radio::after {
        transform: scale(1);
    }

    .period-item__body {
        flex: 1;
    }

    .period-item__name {
        font-size: 15px;
        font-weight: 600;
        color: #fff;
        margin: 0 0 3px;
    }

    .period-item__lessons {
        font-size: 12px;
        color: var(--muted);
    }

    .period-item__save {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        background: var(--green-dim);
        border: 1px solid rgba(74, 222, 128, 0.25);
        color: var(--green);
        flex-shrink: 0;
    }

    .period-item__popular {
        position: absolute;
        top: -1px;
        right: 16px;
        background: linear-gradient(135deg, #26F9FF, #179599);
        color: #04151d;
        font-size: 10px;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 0 0 8px 8px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .pay-card__divider {
        height: 1px;
        background: var(--border);
        margin: 0;
    }

    .pay-card__footer {
        padding: 20px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .pay-price {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .pay-price__label {
        font-size: 11px;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .pay-price__row {
        display: flex;
        align-items: baseline;
        gap: 10px;
    }

    .pay-price__old {
        font-size: 16px;
        color: var(--muted);
        text-decoration: line-through;
    }

    .pay-price__new {
        font-size: 28px;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.5px;
    }

    .pay-price__discount {
        font-size: 11px;
        font-weight: 700;
        color: var(--green);
        background: var(--green-dim);
        border: 1px solid rgba(74, 222, 128, 0.2);
        padding: 2px 8px;
        border-radius: 20px;
    }

    .pay-btn-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .pay-btn {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 14px 32px;
        background: linear-gradient(135deg, #26F9FF, #179599);
        border: none;
        border-radius: 12px;
        color: #04151d;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
        box-shadow: 0 4px 18px rgba(38, 249, 255, 0.22);
        transition: opacity .2s, transform .15s, box-shadow .2s;
    }

    .pay-btn:hover {
        opacity: .88;
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(38, 249, 255, 0.35);
    }

    .pay-btn.locked {
        background: var(--surface-2);
        color: var(--muted);
        box-shadow: none;
        cursor: not-allowed;
        border: 1px solid var(--border);
        opacity: 1;
        transform: none !important;
    }

    .pay-btn.locked:hover {
        opacity: 1;
        transform: none;
        box-shadow: none;
    }

    .pay-btn-tooltip {
        display: none;
        position: absolute;
        bottom: calc(100% + 10px);
        right: 0;
        width: 240px;
        background: #1a3448;
        border: 1px solid rgba(251, 191, 36, 0.3);
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 12.5px;
        color: var(--text);
        line-height: 1.5;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        z-index: 10;
        pointer-events: none;
    }

    .pay-btn-tooltip::after {
        content: '';
        position: absolute;
        bottom: -6px;
        right: 28px;
        width: 10px;
        height: 10px;
        background: #1a3448;
        border-right: 1px solid rgba(251, 191, 36, 0.3);
        border-bottom: 1px solid rgba(251, 191, 36, 0.3);
        transform: rotate(45deg);
    }

    .pay-btn-tooltip i {
        color: var(--yellow);
        margin-right: 5px;
    }

    .pay-btn-wrap:hover .pay-btn-tooltip {
        display: block;
    }

    .hist-card {
        background: var(--surface-1);
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        overflow: hidden;
    }

    .hist-card__title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--muted);
        padding: 18px 20px 14px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .hist-card__title i {
        color: var(--teal);
    }

    .hist-empty {
        padding: 28px 20px;
        text-align: center;
        font-size: 13px;
        color: var(--muted);
    }

    .hist-empty i {
        font-size: 28px;
        display: block;
        margin-bottom: 10px;
        opacity: .3;
    }

    .hist-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        border-bottom: 1px solid rgba(38, 249, 255, 0.06);
    }

    .hist-row:last-child {
        border-bottom: none;
    }

    .hist-row__icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--green-dim);
        border: 1px solid rgba(74, 222, 128, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--green);
        font-size: 14px;
        flex-shrink: 0;
    }

    .hist-row__icon--pending {
        background: rgba(251, 191, 36, 0.08);
        border-color: rgba(251, 191, 36, 0.2);
        color: var(--yellow);
    }

    .hist-row__info {
        flex: 1;
        min-width: 0;
    }

    .hist-row__period {
        font-size: 13px;
        font-weight: 600;
        color: #fff;
    }

    .hist-row__date {
        font-size: 11px;
        color: var(--muted);
        margin-top: 2px;
    }

    .hist-row__amount {
        font-size: 14px;
        font-weight: 700;
        color: var(--green);
        flex-shrink: 0;
    }

    .hist-row__amount--pending {
        color: var(--yellow);
    }

    .pay-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(4, 21, 29, 0.88);
        backdrop-filter: blur(6px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .pay-modal-backdrop.active {
        display: flex;
    }

    .pay-modal {
        background: var(--surface-1);
        border: 1px solid rgba(251, 191, 36, 0.25);
        border-radius: 24px;
        padding: 36px 32px;
        width: 500px;
        max-width: 100%;
        position: relative;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.5);
        animation: modalIn .25s ease;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: translateY(16px) scale(.97);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .pay-modal__close {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: transparent;
        color: var(--muted);
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .2s, color .2s;
    }

    .pay-modal__close:hover {
        background: var(--surface-2);
        color: #fff;
    }

    .pay-modal__icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: rgba(251, 191, 36, 0.08);
        border: 1px solid rgba(251, 191, 36, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 20px;
    }

    .pay-modal h2 {
        font-size: 19px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 16px;
    }

    .pay-modal__body {
        font-size: 13.5px;
        color: rgba(242, 242, 242, 0.75);
        line-height: 1.75;
    }

    .pay-modal__body p {
        margin: 0 0 12px;
    }

    .pay-modal__body p:last-child {
        margin-bottom: 0;
    }

    .pay-modal__body strong {
        color: #fff;
    }

    .pay-modal__actions {
        display: flex;
        gap: 10px;
        margin-top: 28px;
    }

    .pay-modal__btn-confirm {
        flex: 1;
        padding: 13px;
        background: linear-gradient(135deg, #26F9FF, #179599);
        border: none;
        border-radius: 11px;
        color: #04151d;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(38, 249, 255, 0.2);
        transition: opacity .2s, transform .15s;
    }

    .pay-modal__btn-confirm:hover {
        opacity: .88;
        transform: translateY(-1px);
    }

    .pay-modal__btn-cancel {
        padding: 13px 20px;
        background: transparent;
        border: 1px solid var(--border);
        border-radius: 11px;
        color: var(--muted);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s, color .2s;
    }

    .pay-modal__btn-cancel:hover {
        background: var(--surface-2);
        color: #fff;
    }

    @media (max-width: 991px) {
        .pay-wrap {
            padding: 24px 20px 110px;
        }

        .pay-grid {
            grid-template-columns: 1fr;
        }

        .pay-head h1 {
            font-size: 21px;
        }

        .pay-card__footer {
            flex-direction: column;
            align-items: stretch;
        }

        .pay-btn {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="pay-wrap">

    {{-- PAGE HEADER --}}
    <div class="pay-head">
        <div class="pay-head__icon">
            <i class="fas fa-credit-card"></i>
        </div>
        <div>
            <h1>Оплата обучения</h1>
            <p>Выберите период и произведите оплату</p>
        </div>
    </div>

    <div class="pay-grid">

        {{-- ══════════════════════ LEFT ══════════════════════ --}}
        <div class="pay-card">
            <div class="pay-card__header">
                <div class="pay-card__title">Выберите период оплаты обучения</div>
                <div class="pay-card__sub">
                    Ученик: <strong style="color:#fff">{{ $student->full_name ?? '—' }}</strong>
                    &nbsp;·&nbsp;
                    Группа: <strong style="color:var(--teal)">{{ $student->group?->name ?? 'Не назначена' }}</strong>
                </div>
            </div>

            <div class="period-list">
                @foreach($periods as $i => $period)
                <div class="period-item {{ $i === 0 ? 'selected' : '' }}" data-months="{{ $period['months'] }}"
                    data-lessons="{{ $period['lessons'] }}" data-price="{{ $period['price'] }}"
                    data-old="{{ $period['old'] }}">
                    @if($period['popular'])
                    <div class="period-item__popular">Популярный</div>
                    @endif
                    <div class="period-item__radio"></div>
                    <div class="period-item__body">
                        <div class="period-item__name">{{ $period['months'] }} {{ $period['months'] == 1 ? 'месяц' :
                            ($period['months'] < 5 ? 'месяца' : 'месяцев' ) }}</div>
                                <div class="period-item__lessons">{{ $period['lessons'] }} {{ $period['lessons'] < 5
                                        ? 'занятия' : 'занятий' }}</div>
                                </div>
                                @if($period['save'])
                                <div class="period-item__save"><i class="fas fa-tag"></i> Экономия {{ $period['save'] }}
                                    zł</div>
                                @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="pay-card__divider"></div>

                    <div class="pay-card__footer">
                        <div class="pay-price">
                            <div class="pay-price__label">К оплате</div>
                            <div class="pay-price__row">
                                <span class="pay-price__old" id="priceOld">{{ $periods[0]['old'] }} zł</span>
                                <span class="pay-price__new" id="priceNew">{{ $periods[0]['price'] }} zł</span>
                            </div>
                            <div class="pay-price__discount" id="discountBadge">
                                <i class="fas fa-percent" style="margin-right:3px"></i>
                                Скидка −10% для семей с двумя детьми
                            </div>
                        </div>
                        <div class="pay-btn-wrap">
                            <button class="pay-btn {{ ($contract->signed ?? false) ? '' : 'locked' }}" id="btnPay">
                                <i class="fas {{ ($contract->signed ?? false) ? 'fa-credit-card' : 'fa-lock' }}"></i>
                                Оплатить
                            </button>
                            @if(!($contract->signed ?? false))
                            <div class="pay-btn-tooltip">
                                <i class="fas fa-exclamation-triangle"></i>
                                Сначала необходимо <strong>подписать договор</strong> — тогда оплата станет доступна.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════ RIGHT — HISTORY ══════════════════════ --}}
                <div class="hist-card">
                    <div class="hist-card__title">
                        <i class="fas fa-history"></i>
                        История оплат
                    </div>

                    @forelse($payments as $payment)
                    <div class="hist-row">
                        <div
                            class="hist-row__icon {{ $payment->status === 'completed' ? '' : 'hist-row__icon--pending' }}">
                            <i class="fas {{ $payment->status === 'completed' ? 'fa-check' : 'fa-clock' }}"></i>
                        </div>
                        <div class="hist-row__info">
                            <div class="hist-row__period">
                                {{ optional($payment->created_at)->translatedFormat('F Y') ?? '' }}
                            </div>
                            <div class="hist-row__date">
                                {{ optional($payment->paid_at)->format('d.m.Y') ??
                                optional($payment->created_at)->format('d.m.Y') ?? '' }}
                            </div>
                        </div>
                        <div
                            class="hist-row__amount {{ $payment->status === 'completed' ? '' : 'hist-row__amount--pending' }}">
                            {{ number_format((float) $payment->amount, 0) }} zł
                        </div>
                    </div>
                    @empty
                    <div class="hist-empty">
                        <i class="fas fa-receipt"></i>
                        Платежей пока нет
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
                <h2>Важная информация</h2>

                <div class="pay-modal__body">
                    <p>Оплата занятий подтверждает <strong>бронирование места</strong> для вашего ребёнка в выбранной
                        группе, однако не означает немедленного начала обучения.</p>
                    <p>Старт занятий происходит после <strong>полного формирования группы</strong> — когда все родители
                        подпишут договор и произведут оплату.</p>
                    <p>В день проведения первого занятия вы получите <strong>e-mail с подтверждением</strong>
                        официального старта группы. С этого момента производится перерасчёт оплаченной суммы исходя из
                        фактического количества занятий в текущем месяце.</p>
                    <p>Если в месяце проводится <strong>менее 4 занятий</strong>, оставшаяся сумма автоматически
                        переносится на следующий месяц.</p>
                    <p>Мы стараемся сформировать группы и запустить обучение для вас как можно скорее! 🚀</p>
                </div>

                <div class="pay-modal__actions">
                    <button class="pay-modal__btn-cancel" id="cancelModal">Отмена</button>
                    <button class="pay-modal__btn-confirm" id="confirmPay">
                        <i class="fas fa-lock" style="margin-right:7px"></i>
                        Перейти к оплате
                    </button>
                </div>
            </div>
        </div>
        @endsection

        @section('scripts')
        <script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Period selection ── */
    var priceOld = document.getElementById('priceOld');
    var priceNew = document.getElementById('priceNew');
    var selected = document.querySelector('.period-item.selected');

    function selectPeriod(item) {
        document.querySelectorAll('.period-item').forEach(function (i) {
            i.classList.remove('selected');
        });
        item.classList.add('selected');
        selected = item;
        priceOld.textContent = item.getAttribute('data-old')   + ' zł';
        priceNew.textContent = item.getAttribute('data-price') + ' zł';
    }

    var periodList = document.querySelector('.period-list');
    if (periodList) {
        periodList.addEventListener('click', function (e) {
            var item = e.target.closest('.period-item');
            if (item) selectPeriod(item);
        });
    }

    /* ── Open modal on pay click ── */
    document.getElementById('btnPay').addEventListener('click', function () {
        if (this.classList.contains('locked')) return;
        document.getElementById('payModal').classList.add('active');
    });

    /* ── Close modal ── */
    function closeModal() {
        document.getElementById('payModal').classList.remove('active');
    }
    document.getElementById('closeModal').addEventListener('click', closeModal);
    document.getElementById('cancelModal').addEventListener('click', closeModal);
    document.getElementById('payModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    /* ── Confirm → create payment + redirect to iMoje ── */
    document.getElementById('confirmPay').addEventListener('click', function () {
        var btn = this;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:7px"></i>Перенаправляем…';
        btn.disabled = true;

        var months  = selected ? selected.dataset.months  : 1;
        var price   = selected ? selected.dataset.price   : 440;
        var lessons = selected ? selected.dataset.lessons : 4;

        fetch('{{ route("father.payment.create-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                student_id: {{ $student->id ?? 'null' }},
                months:     parseInt(months),
                lessons:    parseInt(lessons),
                amount:     parseFloat(price)
            })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.pay_url && data.fields) {
                // iMoje requires form POST — create hidden form and submit
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = data.pay_url;
                form.style.display = 'none';
                for (var key in data.fields) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = data.fields[key];
                    form.appendChild(input);
                }
                document.body.appendChild(form);
                form.submit();
            } else if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                closeModal();
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-lock" style="margin-right:7px"></i>Перейти к оплате';
            }
        })
        .catch(function () {
            closeModal();
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock" style="margin-right:7px"></i>Перейти к оплате';
        });
    });

});
        </script>
        @endsection