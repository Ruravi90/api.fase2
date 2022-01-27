@extends('layouts.app')

@section('content')
<div class="container">

    <div class="login-box">
      <div class="login-logo">
         <img width="55%" src="{{ asset('img/favicon.png') }}">
      </div>
      <!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">Iniciar sesión</p>

        <form method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
          <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
            <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus placeholder="Usuario">
            @if ($errors->has('username'))
                <span class="help-block">
                    <strong>{{ $errors->first('username') }}</strong>
                </span>
            @endif
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
            <input id="password" type="password" class="form-control" name="password" required placeholder="Contraseña">
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">
             <input id="remember" type="checkbox" name="remember"  {{ old('remember') ? 'checked' : '' }}><label for="remember">&nbsp; Recordarme</label>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Entrar</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <a href="{{ route('password.request') }}">No recuerdo mi contraseña</a><br>
        <a href="{{ route('register') }}" class="text-center">Registrarme</a>
      </div>
      <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
</div>

@endsection

@section('scripts')
  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $('#remember').iCheck({
        checkboxClass: 'icheckbox_flat'
      });
    });
  </script>
@endsection
