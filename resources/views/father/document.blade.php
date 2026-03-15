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
                    <th>{{ __('father.documents.title') }}</th>
                    <th>{{ __('father.documents.number') }}</th>
                    <th>{{ __('father.documents.date') }}</th>
                    <th>Статус</th>
                    <th>{{ __('father.documents.download') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                <tr>
                    <td>{{ $doc->title ?? '—' }}</td>
                    <td>{{ $doc->doc_no ?? '—' }}</td>
                    <td>{{ $doc->sign_date?->format('d.m.Y H:i') ?? '—' }}</td>
                    <td>{{ ($doc->doc_status ?? '') === 'sign' ? 'Подписан' : 'Ожидает подписи' }}</td>
                    <td>
                        <a href="{{ route('father.document.view', $doc->id) }}" class="father-btn father-btn--sm">Открыть</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
