@extends('admin.layout.theme.default')

@section('title', __('errors.main_title'))

@section('content')
    <div style="text-align: center; padding: 100px;">
        <h1 style="font-size: 80px;">503 - {{ __('errors.503_title') }}</h1>
        <p>{{ __('errors.503_message') }} 🛠️</p>
        <a href="{{ url('/') }}">{{ __('errors.btn_main') }}</a>
    </div>
@endsection
