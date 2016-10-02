@extends('layouts.main')

@section('content')
@if(Session::has('flash_message'))
{{Session::get('flash_message')}}
@endif
	<div class="callout primary">
<div class="row column">
<h3>Reservas</h3>

</div>
</div>
<div class="row small-up-2 medium-up-3 large-up-4">


<table>
  <tr>
    <th>Nombre</th>
    <th>Plazas reservadas</th>
    <th>Alojamiento</th>
    <th>Habitación</th>
    <th>Dia reservado</th>
    <th>Hora reservada</th>
    <th>Subida</th>
    <th>Bajada</th>
    <th>Imprimir</th>


  </tr>

  <?php foreach($reservas as $reserva) : ?>

  <tr>
    <td>{{$reserva->nombre}}</td>
    <td>{{$reserva->numero_plazas}}</td>
    <td>{{$reserva->alojamiento}}</td>
    <td>{{$reserva->numero_habitacion}}</td>
    <td>{{$reserva->dia}}</td>
    <td>{{$reserva->salida}}</td>
    <td>{{$reserva->parada_subida}}</td>
    <td>{{$reserva->parada_bajada}}</td>
    <td>
      <!-- <form method="GET" action="http://<?php //echo Request::getHost()?>/reserva/imprimir/{{$reserva->id}}">

        <i class="fi-print size-24"><input value="Print" class="" type="submit"></i>  

      </form> -->

      <a class="print-icon" href="http://<?php echo Request::getHost()?>/reserva/imprimir/{{$reserva->id}}" target="_blank"><i class="fi-print size-24"></i></a>

    </td>

  </tr>	

  <?php endforeach; ?>

</table> 





</div>
@stop