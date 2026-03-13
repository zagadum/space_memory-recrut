@extends('father.layouts.app')
@section('title', __('father.payments.title'))

@section('content')
<div class="father-section">
    <h1 class="father-section__title">{{ __('father.payments.title') }}</h1>

    {{-- Payment form --}}
    <div class="father-section father-section--highlight">
        <h2 class="father-section__subtitle">{{ __('father.payments.make_payment') }}</h2>

        <form method="POST" action="{{ route('father.payment.create-order') }}">
            @csrf

            <div class="father-form-group">
                <label class="father-label">{{ __('father.payments.program') }}</label>
                <select name="project_code" required class="father-input">
                    <option value="space_memory">Space Memory</option>
                    <option value="indigo">Speedy Mind Indigo</option>
                </select>
            </div>

            <div class="father-form-group">
                <label class="father-label">{{ __('father.payments.amount') }} (PLN)</label>
                <input type="number" name="amount" required min="1" step="0.01" 
                       class="father-input" placeholder="440.00" value="440.00">
            </div>

            <button type="submit" class="father-btn father-btn--primary father-btn--full">
                💳 {{ __('father.payments.pay_now') }}
            </button>
        </form>

        <div class="father-test-cards">
            <p class="father-hint">{{ __('father.payments.sandbox_hint') }}</p>
            <code>Visa: 4111 1111 1111 1111 · 12/29 · CVV: 123</code>
        </div>
    </div>

    @if($transactions->isEmpty())
        <p class="father-empty">{{ __('father.payments.pending') }}</p>
    @else
        <table class="father-table">
            <thead>
                <tr>
                    <th>{{ __('father.payments.date') }}</th>
                    <th>{{ __('father.payments.amount') }}</th>
                    <th>{{ __('father.payments.status') }}</th>
                    <th>{{ __('father.documents.download') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr>
                    <td>{{ $tx->paid_at?->format('d.m.Y') ?? $tx->created_at?->format('d.m.Y') }}</td>
                    <td>{{ number_format((float)$tx->amount, 2, ',', ' ') }} {{ $tx->currency ?? 'PLN' }}</td>
                    <td>
                        <span class="father-badge father-badge--{{ $tx->status === 'completed' ? 'success' : 'warning' }}">
                            {{ $tx->status === 'completed' ? __('father.payments.status_completed') : __('father.payments.status_pending') }}
                        </span>
                    </td>
                    <td>
                        @php
                            $invoice = \App\Models\GlsInvoiceDocument::where('transaction_id', $tx->id)->first();
                        @endphp
                        @if($invoice)
                            <a href="{{ route('father.download-invoice', $invoice->id) }}" class="father-btn father-btn--sm">
                                {{ __('father.documents.download') }}
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
