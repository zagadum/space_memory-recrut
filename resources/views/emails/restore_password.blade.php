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
                <div id="subject"><b>Відновлення пароля по E-mail</b></div>
            </td></tr>
        <!-- Text -->
        <tr><td>
                <div id="content">
                    <p class="sP">Вітаю, <b>{{$username}}</b>!</p>
                    <p class="sP">Хтось, можливо ви, ініціював процедуру відновлення пароля в особистий кабінет  Vilno.org.</p>

                    <p class="sP">На ваш E-mail зареєстрований користувач:  {{$email}}</p>

                    <p class="sP">ЯКЩО відновлення пароля ініційовано вами, то пройдіть по  <a href="{{$restore_url}}" class="sA" target="_blank">посиланню</a>,та вкажіть новий пароль </p>
                    <p class="sP">ЯКЩО ви не хочете відновлювати пароль, то просто ігноруйте це повідомлення .</p>
                    <p class="sP"><br/>З повагою,<br/> Служба підтримки "Bільно!"</p>
                </div>
            </td></tr>
    </table>
</div>
</body>
</html>
