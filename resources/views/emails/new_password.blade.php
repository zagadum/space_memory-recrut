{{--<div>--}}
{{--    {{ trans('admin.admin-user.actions.change_password') }}--}}
{{--</div>--}}
{{--<div>--}}
{{--    {{ trans('admin.admin-user.actions.new_password') }}: {{ $newEmail->password }}--}}
{{--</div>--}}
{{--@if(isset($newEmail->link))--}}
{{--<form method="post"  action="{{ $newEmail->link }}">--}}
{{--    <input type="hidden" name="password" value="{{$newEmail->password}}">--}}
{{--    <input type="hidden" name="uuid" value="{{$newEmail->uuid}}">--}}
{{--    <button class="btn btn-success" type="submit">Применить</button>--}}
{{--</form>--}}
{{--@endif--}}


<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style type="text/css">
        b { color: #333; }
        .sP {font-family:Arial,Helvetica,sans-serif; font-size:12px;}
        .sA {color:blue;}
        .sH2 {font-family:Arial,Helvetica,sans-serif; font-size:13px; font-weight:bold;}
        .sP {font-family:Arial,Helvetica,sans-serif; font-size:12px;}
        .sLI {font-family:Arial,Helvetica,sans-serif; font-size:12px;}
        .sUL {margin-top: 0px; padding-left: 30px;}
        #subject {font-family:Arial,Helvetica,sans-serif; font-size:15px; margin-top:6px; margin-left:10px; margin-right:10px; margin-bottom:5px;}
        #content {margin: 8px 9px;}
    </style>
</head>
<body>
<div>
    <table style="width:90%"  align="center" cellpadding="0" cellspacing="0" border="0">
        <!-- Subject -->
        <tr><td style="background-color:#e5e5e5 ">
                <div id="subject"><b>  {{ trans('admin.admin-user.actions.change_password') }}</b></div>
            </td></tr>
        <!-- Text -->
        <tr><td>
                <div id="content">
                    <p class="sP">Вітаю, <b>{{$newEmail->name}}</b>!</p>
                    <p class="sP">Хтось, можливо ви, ініціював процедуру зміни паролю в особистому кабінеті  Vilno.org.</p>
                    <p class="sP">{{ trans('admin.admin-user.actions.new_password') }}: {{ $newEmail->password }}</p>

                    @if(isset($newEmail->link))
                        <p class="sP">ЯКЩО зміна паролю ініційована вами, то пройдіть по посиланню </p>
                        <form method="post"  action="{{ $newEmail->link }}">
                            <input type="hidden" name="password" value="{{$newEmail->password}}">
                            <input type="hidden" name="uuid" value="{{$newEmail->uuid}}">
                            <button class="btn btn-success" type="submit">Перейти</button>
                        </form>
                        <p class="sP"><br/>З повагою,<br/> Служба підтримки "Bільно!"</p>
                    @endif
                </div>
            </td></tr>
    </table>
</div>
</body>
</html>
