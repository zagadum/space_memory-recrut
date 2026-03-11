
@extends('admin.layout.theme.default')
@section('content')
<?php

$listIn=old();
if (!empty($listIn)){
    foreach ($listIn as $k=>$v){
        if (empty($firmsInfo[$k]) &&  old($k)){
            $firmsInfo[$k]=old($k);

        }
    }
}


?>
<div class="container">
  <div class="row ">
    <div class="panel panel-default register-from">
      <!-- <div class="panel-heading">Register</div> -->
      <div class="panel-body">

        <form class="register-form-horizontal" role="form" method="POST" action="{{ url('/registered') }}">
          {{ csrf_field() }}
          <h1>Реєстрація</h1>

          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="control-label"> Тип користувача</label>
            <div>
              <select class="inp_s select-hidden" name="type_profile">
                  <option value="user" @if (old('type_profile')=='user') selected="selected "@endif>Учень</option>
                <option value="company" @if (old('type_profile')=='company') selected="selected "@endif >Вчитель</option>


              </select>

              @if ($errors->has('name'))
              <span class="help-block">
                <strong style="color: red">{{ $errors->first('name') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <div class="input-wrapper">
              <label for="name" class="input-label">
                <i class="fa fa-user-o"></i>
                <p class="simple-p input-label-text">Ваше ім'я</p>
              </label>
              <div>
                <input id="name" type="text" class="input-field" name="name" value="{{ old('name') }}" required="required">
                @if ($errors->has('name'))
                <span class="help-block">
                  <strong style="color: red">{{ $errors->first('name') }}</strong>
                </span>
                @endif
              </div>
            </div>
          </div>

          <div class="form-group{{ $errors->has('tel') ? ' has-error' : '' }}">
            <div class="input-wrapper">
              <label for="tel" class="input-label">
                <i class="fa fa-phone-alt" aria-hidden="true"></i>
                <p class="simple-p input-label-text">Телефон</p>
              </label>
              <div>
                <input id="tel" type="tel"name="tel" value="{{ old('tel') }}" class="input-field" maxlength="20">
                @if ($errors->has('tel'))
                <span class="help-block">
                  <strong style="color: red">{{ $errors->first('tel') }}</strong>
                </span>
                @endif
              </div>
            </div>
          </div>

          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <div class="input-wrapper">
              <label for="email" class="input-label">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <p class="simple-p input-label-text">E-Mail</p>
              </label>
              <div>
                <input id="email" type="email" class="input-field" name="email" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                <span class="help-block">
                  <strong style="color: red">{{ $errors->first('email') }}</strong>
                </span>
                @endif
              </div>
            </div>
          </div>

          <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <div class="input-wrapper">
              <label for="password" class="input-label">
                <i class="fa fa-key" aria-hidden="true"></i>
                <p class="simple-p input-label-text">Пароль</p>
              </label>
              <div>
                <input id="password" type="password" class="input-field" name="password" required>
                @if ($errors->has('password'))
                <span class="help-block">
                  <strong style="color: red">{{ $errors->first('password') }}</strong>
                </span>
                @endif
              </div>
            </div>
          </div>

          <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
            <div class="input-wrapper">
              <label for="password-confirm" class="input-label">
                <i class="fa fa-key" aria-hidden="true"></i>
                <p class="simple-p input-label-text">Повторіть пароль</p>
              </label>
              <div>
                <input id="password-confirm" type="password" class="input-field" name="password_confirmation" required>
                @if ($errors->has('password_confirmation'))
                <span class="help-block">
                  <strong style="color: red">{{ $errors->first('password_confirmation') }}</strong>
                </span>
                @endif
              </div>
            </div>
          </div>

          <div class="register-btn-container">
            <button type="submit" class="inp_b">
              Зареєструватися
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    const phoneFiled = document.querySelector("#tel");
  const pattern = /[^0-9+\s-]+/gi;
  phoneFiled.addEventListener("input", validatePhoneFiled)

  function validatePhoneFiled(e) {
    phoneFiled.value = phoneFiled.value.replace(pattern, '')
    console.log(phoneFiled.value.replace(pattern, ''));
  }
</script>
@endsection
