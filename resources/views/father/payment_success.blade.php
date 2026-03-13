@extends('father.layouts.app')
@section('title', __('father.payments.success_title'))

@section('content')
<div class="father-section" style="text-align: center; margin-top: 5rem;">
    <h1 class="father-section__title" style="color: var(--success-color);">{{ __('father.payments.success_title') }}</h1>
    <p style="margin-bottom: 2rem;">{{ __('father.payments.success_text') }}</p>
    <a href="{{ route('father.payment') }}" class="father-btn">{{ __('father.payments.back') }}</a>
</div>
@endsection
