@extends('layouts.main')

@section('content')
<div class="container">
<div  style="position:center; width: 40%; margin: auto; min-height: 682px">
<h1 style="text-align: center">Зворотній зв'язок</h1>
    <form action="/feedback" method="POST">
        @csrf
        <div class="form-row">
            <div class="col">
                <label for="recipient-name" class="col-form-label">Ваше ім'я:</label>
                <input required type="text" name="name" value="{{old('name')}}" class="form-control" >
            </div>
        </div>
        <div class="form-group">
            <label for="recipient-mail" class="col-form-label ">Ваш email:</label>
            <input required type="email" name="email" id="recipient-mail" class="input-field form-control  is-invalid" value="{{old('email')}}" placeholder="example@mail.ru">

        </div>
        <div class="form-group">
            <label for="recipient-phone" class="col-form-label">Ваш телефон:</label>
            <input required type="number" value="{{old('phone')}}" id="recipient-phone" class="form-control only_number" name="phone">
            <style>
                .only_number {
                    -moz-appearance: textfield;
                }
                .only_number::-webkit-inner-spin-button {
                    display: none;
                }
            </style>
        </div>
        <div class="form-group">
            <label for="message-text" class="col-form-label">Повідомлення:</label>
            <textarea required name="comments" value="{{old('comments')}}"  id="message-text" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom: 20px; float: right">Надіслати повідомлення</button>
    </form>
</div>
</div>




@stop
