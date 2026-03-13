@extends('father.layouts.app')
@section('title', __('father.payments.fail_title'))

@section('content')
<div class="father-section" style="text-align: center; margin-top: 5rem;">
    <h1 class="father-section__title" style="color: #ef4444;">{{ __('father.payments.fail_title') }}</h1>
    <p style="margin-bottom: 2rem;">{{ __('father.payments.fail_text') }}</p>
    <a href="{{ route('father.payment') }}" class="father-btn">{{ __('father.payments.retry') }}</a>
</div>
@endsection
