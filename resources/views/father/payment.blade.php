@extends('father.layouts.app')
@section('title', __('father.payments.title'))

@section('content')
<div class="father-section">
    <h1 class="father-section__title">{{ __('father.payments.title') }}</h1>

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
                        <span class="father-badge father-badge--{{ $tx->status === 'paid' ? 'success' : 'warning' }}">
                            {{ $tx->status === 'paid' ? __('father.payments.paid') : __('father.payments.pending') }}
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
