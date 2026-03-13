@extends('father.layouts.app')

@section('content')
<div class="father-section" style="text-align: center; margin-top: 5rem;">
    <h1 class="father-section__title" style="color: var(--error-color);">{{ __('father.payments.fail_title') ?? 'Płatność nieudana' }}</h1>
    <p class="father-login__subtitle" style="margin-bottom: 2rem;">
        {{ __('father.payments.fail_text') ?? 'Wystąpił problem podczas przetwarzania płatności. Spróbuj ponownie.' }}
    </p>

    <a href="{{ route('father.payment') }}" class="father-btn father-btn--primary">
        {{ __('father.payments.retry') ?? 'Spróbuj ponownie' }}
    </a>
</div>
@endsection
