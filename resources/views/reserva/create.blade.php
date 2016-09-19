@extends('layouts.main')

@section('content')
    <div class="callout primary">
<div class="row column">
<h3>Formulario de Reserva</h3>
<p class="lead">Introducir los datos de la reserva</p>
</div>
</div>
<div class="row small-up-2 medium-up-3 large-up-4">
    <div class="main">

        @if (count($errors) > 0)
        <div class="callout alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif


        {!! Form::open(array('action' => 'ReservaController@store', 'enctype' => 'multipart/form-data')) !!}

        <div class="large-6 columns">
            {!! Form::label('name', 'Nombre') !!}
            {!! Form::text('name', $value = null, $attributes = ['placeholder' => 'Nombre', 'name' => 'nombre']) !!}
        </div>

        <div class="large-6 columns">
            {!! Form::label('apellidos', 'Apellidos') !!}
            {!! Form::text('name', $value = null, $attributes = ['placeholder' => 'Apellidos', 'name' => 'apellidos']) !!}
        </div>    

        <div class="large-6 columns">
            {!! Form::label('alojamiento', 'Alojamiento') !!}
            {!! Form::text('name', $value = null, $attributes = ['placeholder' => 'Alojamiento', 'name' => 'alojamiento']) !!}
        </div>

        <div class="large-4 columns">
            {!! Form::label('numero_habitacion', 'Número de habitación') !!}
            {!! Form::text('name', $value = null, $attributes = ['placeholder' => 'Número de Habitación', 'name' => 'numero_habitacion']) !!}

        </div>

        <div class="large-4 columns">
            {!! Form::label('dia_reservado', 'Día reservado') !!}
            
            <input id="dia_reservado" name="dia_reservado" type="text" value="<?php echo (date('d-m-Y')); ?>"></input>
        </div>

        <div class="large-4 columns">

            {!! Form::label('horario_reservado', 'Horario reservado') !!}
            {!! Form::select('horario_reservado', array('0' => '10:00', '1' => '17:00'), '0'); !!}
        
        </div>

        <div class="large-12 columns"></div>

        <div class="large-4 columns">

            {!! Form::label('parada_subida', 'Parada de subida') !!}
            {!! Form::select('parada_subida', array('0' => 'Puerto rico', '1' => 'Arguineguín', '2' => 'Maspalomas', '3' => 'Playa del Inglés'), '0'); !!}
        
        </div>

        <div class="large-4 columns">
            {!! Form::label('parada_bajada', 'Parada de bajada') !!}
            {!! Form::select('parada_bajada', array('0' => 'Puerto rico', '1' => 'Arguineguín', '2' => 'Maspalomas', '3' => 'Playa del Inglés'), '3'); !!}
        
        </div>

        <div class="large-12 columns">
            {!! Form::submit('Enviar', $attributes = ['class' => 'button']) !!}
            {!! Form::close() !!}
        </div>

        
    </div>
</div>
@stop