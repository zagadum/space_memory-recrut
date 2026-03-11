@extends('layouts.main')
@section('content')

<main class="main sections sections__main">
  <div id="multicolssection1" class="section container-xl section__default" style="min-height:550pt">
    <div class="bg position-absolute section-bg"></div>
    <hr class="divider section-divider d-none">
    <div class="section-inner">
      <div class="sections section-sections row">
        <div id="content" class="section col-12 col-lg section__default ">
          <div class="bg position-absolute section-bg bg-transparent"></div>
          <hr class="divider section-divider d-none">
          <div class="section-inner">
            <div class="section-content">
              <div class="post-content">
                <div class="main">
                  <div class="content container-fluid mw90 d-flex justify-content-center">

                    <form class="login-form form-horizontal" role="form" method="POST" action="{{ url('/forgot-password') }}">
                      <h3>Скинути пароль</h3>
                      {{ csrf_field() }}

                      <div class="form-group">
                        <label for="email" class="input-label">
                          <i class="fa fa-envelope" aria-hidden="true"></i>
                          <p class="simple-p input-label-text">E-Mail</p>
                        </label>
                        <!-- <label for="email" class="control-label">E-Mail</label> -->
                        <input id="email" type="email" class="input-field" name="email" value="{{ @$email or old('email') }}" required autofocus>

                        @if ($errors->has('email'))
                        <span class="help-block">
                          <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                      </div>

                      <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="input-label">
                          <i class="fa fa-key"></i>
                          <p class="simple-p input-label-text">Пароль</p>
                        </label>
                        <input id="password" type="password" class="input-field" name="password" required>

                        @if ($errors->has('password'))
                        <span class="help-block">
                          <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                      </div>

                      <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <label for="password-confirm" class="input-label">
                          <i class="fa fa-key"></i>
                          <p class="simple-p input-label-text">Підтвердьте Пароль</p>
                        </label>
                        <input id="password-confirm" type="password" class="input-field" name="password_confirmation" required>

                        @if ($errors->has('password_confirmation'))
                        <span class="help-block">
                          <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </span>
                        @endif
                      </div>

                      <div style="text-align:center">
                        <button type="submit" class="round-button">
                          Скинути пароль
                        </button>
                      </div>
                    </form>


                  </div><!-- /.post-content -->
                </div>
                <!--/.section-content -->
              </div><!-- /.section-inner -->
            </div><!-- /#content.section col-12 col-lg section__default section__post post post__detail post__default-->

          </div>
          <!--/.sections section-sections row-->
        </div><!-- /.section-inner -->
      </div><!-- /#content.section col-12 col-lg section__default section__post post post__detail post__default-->

    </div>
    <!--/.sections section-sections row-->
  </div><!-- /.section-inner -->
  </div><!-- /#content.section col-12 col-lg section__default section__post post post__detail post__default-->
</main><!-- /.sections main-sections -->

@endsection