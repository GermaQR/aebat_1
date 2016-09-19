@extends('layouts.main')

@section('title', 'Login')

@section('content')

<div class="callout primary">
  <div class="row column">
    <h3>Login</h3>
    <p class="lead">Por favor, introduzca los datos de acceso</p>
  </div>
</div>

<div class="row small-up-2 medium-up-3 large-up-4">
    <div class="main">
        <div class="large-6 columns">
            <form method='POST' action='/auth/login'>
                {!! csrf_field() !!}
                <div>
                    Email: <input type='email' name='email' value='{{old('email')}}'>
                </div>
                <div>
                    Contraseña: <input type='password' name='password' id='password'>
                </div>
                <div>
                    <input type='checkbox' name='remember'> Recordarme
                </div>
                <div>
                    <button class="button" type='submit'>Login</button>
                </div>
            </form>
        </div>    
    </div>    
</div>
@stop