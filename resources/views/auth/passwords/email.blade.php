<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pressure Dx</title>
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body class="login">
<div class="main">
    <div class="content">
        <a class="navbar-brand" href="/">
            <img src="/images/logo-white.png" alt="logo" width="266" height="106">
        </a>
  
                    <form class="login-form form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}" style="min-width:450px">
                        <h3>Reset Password</h3>
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-3 control-label">E-Mail Address</label>

                                    <div class="col-md-8">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
								 <div  style="text-align:center; margin-left:55px">
                            <button type="submit" class="btn btn-primary">
                                Send Password Reset Link
                            </button>
                        </div>
                            </div>
                        </div>
                       
                    </form>
              
         
      
    </div>
</div>

 

<footer class="footer">
    <span class="copyright">© 2017</span>
</footer>

<script src="/js/jquery-1.11.3.min.js"></script>
<script src="/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#info_box').modal('show');
    });
</script>
</body>
</html>


 