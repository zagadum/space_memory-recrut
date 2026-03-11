<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "//www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="//www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{$data['mailData']['subject']}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>

<body style="margin: 0; padding: 0; font-family: Segoe UI;">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
    <tr>
        <td>

<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>

        </td>
    </tr>
    <tr>
        <td><p><br /></p></td>
    </tr>
    <tr>
        <td>
            <h3 align="center">Доброго дня, {{$data['userInfo']['name']}}!</h3>
            <p>Поздоровляємо Вас з успішною реєстрацією на <a href="http://vilno.org" target="_blank">Національному соціальному порталi Вільно!</a></p>
            @if (!empty($data['userInfo']['login']))
                Ваш логін для доступу в персональний кабінет: <b>{{@$data['userInfo']['login']}}</b><br />
                Ваш пароль: <b>{{@$data['userInfo']['password']}}</b><br />
                <p>Для активації Вашого облікового запису на порталі, будь ласка підтвердіть свою адресу електронної пошти, перейшовши за <a href="http://vilno.org/activate/101231447556" target="_blank">цим посиланням</a>.</p>
            @endif

            <blockquote style="margin-left: 350px;">
                <cite>З повагою,<br />
                адміністрація порталу.</cite>
            </blockquote>
        </td>
    </tr>
</table>

        </td>
    </tr>
</table>
        </td>
    </tr>
</table>

</body>
</html>
