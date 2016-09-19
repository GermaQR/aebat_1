@extends('layouts.main')

@section('content')
@if(Session::has('flash_message'))
{{Session::get('flash_message')}}
@endif
<div class="callout primary">
  <div class="row column">
    <h3>Mensaje</h3>
  </div>
</div>
<div class="large-6 columns">
  <div class="callout alert">
    <h5>{{$mensaje}}</h5>
  </div>
</div>
@stop