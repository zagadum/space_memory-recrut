@extends('admin.layout.theme.default')
@section('title', 'Помилка бази даних')

@section('content')
    <div style="text-align: center; padding: 100px;">
        <h1>💥 Ой! Проблема з базою даних</h1>
        <p>Наші гноми вже все лагодять.</p>
        <a href="{{ url('/') }}">На головну</a>
    </div>
@endsection
