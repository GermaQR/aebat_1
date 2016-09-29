<!doctype html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Gestión de Reservas</title>
<link rel="stylesheet" type="text/css" href="/css/foundation.css">
<link rel="stylesheet" type="text/css" href="/css/foundation-icons.css">
<link rel="stylesheet" type="text/css" href="/css/app.css">
<link rel="stylesheet" type="text/css" href="/css/jquery-ui.css">

  
</head>
<body>
<div class="off-canvas-wrapper">
<div class="off-canvas-wrapper-inner" data-off-canvas-wrapper>
<div class="off-canvas position-left reveal-for-large" id="my-info" data-off-canvas data-position="left">
<div class="row column">

	<img class="logo" src="/css/images/bus-aebat-2.png" height="120" width="120">

<br>

<h5>Menu Principal</h5>
<ul class="side-nav">
	<li><a href="/">Inicio</a></li>
	@if(!Auth::guest())
		<li><a href="/reserva/create">Crear Reserva</a></li>
	@endif
	@if(!Auth::guest())	
		<li><a href="/reserva">Ver Reservas</a></li>
	@endif
	@if(Auth::guest())
		<li><a href="/auth/login">Acceso</a></li>
	@endif
	@if(Auth::check() && (Auth::user()->name == 'admin'))
		<li><a href="/auth/register">Registro</a></li>
	@endif
	@if(!Auth::guest())	
		<li><a href="/viaje/buscar">Buscar Ruta</a></li>
		<li><a href="/auth/logout">Salir</a></li>
	@endif	
</ul>
</div>
</div>
<div class="off-canvas-content" data-off-canvas-content>
<div class="title-bar hide-for-large">
<div class="title-bar-left">
<button class="menu-icon" type="button" data-open="my-info"></button>

</div>
</div>
<!-- @if(Session::has('message'))
<div data-alert class="alert-box">
	{{Session::get('message')}}
</div>
@endif -->

@yield('content')
<hr>
</div>
</div>
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script src="/js/foundation.js"></script>
<script src="/js/app.js"></script>
<script>
      $(document).foundation();
</script>
<script>
  $( function() {

		$('#dia_reservado').datepicker({dateFormat: 'dd-mm-yy'});
  });
</script>
</body>
</html>
