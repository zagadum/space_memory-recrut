@extends('father.layouts.app')
@section('title', __('father.documents.title'))

@section('content')
<div class="father-section">
    <h1 class="father-section__title">{{ __('father.documents.title') }}</h1>

    @if($documents->isEmpty())
        <p class="father-empty">{{ __('father.documents.no_documents') }}</p>
    @else
        <table class="father-table">
            <thead>
                <tr>
                    <th>{{ __('father.documents.number') }}</th>
                    <th>{{ __('father.documents.date') }}</th>
                    <th>{{ __('father.documents.amount') }}</th>
                    <th>{{ __('father.documents.download') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                <tr>
                    <td>{{ $doc->number ?? '—' }}</td>
                    <td>{{ $doc->issue_date?->format('d.m.Y') ?? '—' }}</td>
                    <td>{{ number_format((float)$doc->amount_gross, 2, ',', ' ') }} {{ $doc->currency ?? 'PLN' }}</td>
                    <td>
                        <a href="{{ route('father.download-invoice', $doc->id) }}" class="father-btn father-btn--sm">
                            {{ __('father.documents.download') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
