@extends('father.layouts.app')
@section('title', __('father.payments.processing'))

@section('content')
<div class="father-section" style="text-align: center; margin-top: 5rem;">
    <h1 class="father-section__title">{{ __('father.payments.processing') }}...</h1>
    <div style="margin-bottom: 2rem;">
        <!-- Simple pure CSS spinner -->
        <div style="border: 4px solid var(--border-color); border-top: 4px solid var(--primary-color); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: auto;"></div>
        <style> @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } } </style>
    </div>
</div>
@endsection
