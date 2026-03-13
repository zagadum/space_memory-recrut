@extends('father.layouts.app')

@section('content')
<div class="father-section" style="text-align: center; margin-top: 5rem;">
    <h1 class="father-section__title" style="color: var(--success-color);">{{ __('father.payments.success_title') ?? 'Płatność udana!' }}</h1>
    <p class="father-login__subtitle" style="margin-bottom: 2rem;">
        {{ __('father.payments.success_text') ?? 'Twoja płatność została pomyślnie przetworzona. Faktura będzie wkrótce dostępna do pobrania.' }}
    </p>

    <a href="{{ route('father.payment') }}" class="father-btn father-btn--primary">
        {{ __('father.payments.back') ?? 'Wróć do płatności' }}
    </a>
</div>
@endsection
