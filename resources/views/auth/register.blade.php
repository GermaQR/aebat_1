@extends('layouts.main')

@section('title', 'Register')

@section('content')

<div class="callout primary">
  <div class="row column">
    <h3>Registro</h3>
    <p class="lead">Por favor, introduzca sus datos</p>
  </div>
</div>

<div class="row small-up-2 medium-up-3 large-up-4">
    <div class="main">
        <div class="large-6 columns">
            <form method='POST' action='/auth/register'>
                {!! csrf_field() !!}
                 <div>
                    Name: <input type='text' name='name' value='{{old('name')}}'>
                </div>
                <div>
                    Email: <input type='email' name='email' value='{{old('email')}}'>
                </div>
                <div>
                    Password: <input type='password' name='password'>
                </div>
                
                <div>
                    Confirm Password: <input type='password' name='password_confirmation'>
                </div>
                <div>
                    <button class="button" type='submit'>Register</button>
                </div>
            </form>
        </div>
    </div>
</div>            
@stop