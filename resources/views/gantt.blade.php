@extends('layouts.app')

@section('css')
<!-- <link href="dhtmlxgantt.css"rel="s'tylesheet"> -->
<link href="{{ asset('css/dhtmlx-skins/'.$css_skin.'.css') }}" rel="stylesheet">
<link href="{{ asset('css/gantt/main.css') }}" rel="stylesheet">
@endsection

@section('content')
<!-- ALERT -->
<div id="toast-success" class="toast align-items-center text-white bg-success border-0 start-50 translate-middle-x" style="position: absolute;" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="d-flex">
    <div id="toast-success-body" class="toast-body">
      Hello, world! This is a toast message.
    </div>
  </div>
</div>
<!-- ERROR -->
<div id="toast-error" class="toast align-items-center text-white bg-danger border-0 start-50 translate-middle-x" style="position: absolute;" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="d-flex">
    <div id="toast-error-body" class="toast-body">
      Hello, world! This is a toast message.
    </div>
  </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Gantt container -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-1">
	@if(!Auth::user()->isProfesor())
	<div class="btn-toolbar mb-2 mb-md-0">
		<div id="grupo-token" class="btn-group mx-2 {{($token_owner == null)?'d-none':''}}">
			<button id="refrescar-gantt" type="button" class="btn btn-sm btn-primary"
				data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Refrescar datos del gantt. Además vuelve a dibujarlo en pantalla." data-bs-trigger="hover">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
					<path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
					<path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
				</svg>
			</button>
			<button id="soltar-token" type="button" class="btn btn-sm btn-primary primary {{($token_owner != null && $token_owner == Auth::user()->id)?'':'d-none'}}"
				data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Soltar token" data-bs-trigger="hover">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-activity" viewBox="0 0 16 16">
					<path fill-rule="evenodd" d="M6 2a.5.5 0 0 1 .47.33L10 12.036l1.53-4.208A.5.5 0 0 1 12 7.5h3.5a.5.5 0 0 1 0 1h-3.15l-1.88 5.17a.5.5 0 0 1-.94 0L6 3.964 4.47 8.171A.5.5 0 0 1 4 8.5H.5a.5.5 0 0 1 0-1h3.15l1.88-5.17A.5.5 0 0 1 6 2Z"/>
				</svg>
			</button>
			<button id="pedir-token" type="button" class="btn btn-sm btn-primary {{($token_owner != null && $token_owner != Auth::user()->id)?'':'d-none'}}"
				data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Pedir token" data-bs-trigger="hover">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-index-fill" viewBox="0 0 16 16">
					<path d="M8.5 4.466V1.75a1.75 1.75 0 1 0-3.5 0v5.34l-1.2.24a1.5 1.5 0 0 0-1.196 1.636l.345 3.106a2.5 2.5 0 0 0 .405 1.11l1.433 2.15A1.5 1.5 0 0 0 6.035 16h6.385a1.5 1.5 0 0 0 1.302-.756l1.395-2.441a3.5 3.5 0 0 0 .444-1.389l.271-2.715a2 2 0 0 0-1.99-2.199h-.581a5.114 5.114 0 0 0-.195-.248c-.191-.229-.51-.568-.88-.716-.364-.146-.846-.132-1.158-.108l-.132.012a1.26 1.26 0 0 0-.56-.642 2.632 2.632 0 0 0-.738-.288c-.31-.062-.739-.058-1.05-.046l-.048.002z"/>
				</svg>
			</button>
		</div>
		<div id="grupo-voto" class="btn-group mx-2">
			<button id="aceptar-pedido" type="button" class="btn btn-sm btn-success glows"
				data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Aceptar el pedido de token" data-bs-trigger="hover">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-send-check-fill" viewBox="0 0 16 16">
					<path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47L15.964.686Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
					<path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686Z"/>
				</svg>
			</button>
			<button id="rechazar-pedido" type="button" class="btn btn-sm btn-danger glowr"
				data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Rechazar el pedido de token" data-bs-trigger="hover">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-x-fill" viewBox="0 0 16 16">
					<path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47L15.964.686Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
					<path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm-4.854-1.354a.5.5 0 0 0 0 .708l.647.646-.647.646a.5.5 0 0 0 .708.708l.646-.647.646.647a.5.5 0 0 0 .708-.708l-.647-.646.647-.646a.5.5 0 0 0-.708-.708l-.646.647-.646-.647a.5.5 0 0 0-.708 0Z"/>
				</svg>
			</button>
		</div>
		<ul class="dropdown-menu" aria-labelledby="token-dropdown">
			<li><a id="pedir-token" class="dropdown-item text-primary" href="#" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Pedir token para trabajar en el gantt" data-bs-trigger="hover">Peidir token</a></li>
			<li><a id="aceptar-token" class="dropdown-item text-success" href="#" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Aceptar la votación de obtención de token" data-bs-trigger="hover">Aceptar votación</a></li>
			<li><a id="rechazar-token" class="dropdown-item text-danger" href="#" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Rechazar la votación de obtención de token" data-bs-trigger="hover">Rechazar votación</a></li>
			<li><hr id="token-separator" class="dropdown-divider"></li>
			<li><a id="soltar-token" class="dropdown-item text-primary" href="#" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Soltar el token de trabajo" data-bs-trigger="hover">Soltar token</a></li>
  		</ul>
	</div>
	@endif
	<h1 class="h2 mb-0 ms-5 text-center">Sprint #{{$sprint['numero']}}</h1>
	@if(!$sprint['iniciado'])
		<h4 class="fw-bold mb-0 me-5 text-primary">Comienza el día {{$sprint['comienzo'];}}</h4>
	@else
		<h4 class="fw-bold mb-0 me-5 {{$text_color}}">Deadline: {{$sprint['deadline'];}} {{($date_diff  >= 0) ? " - Faltan ".$date_diff." días.": " - Sprint entregado"}}</h4>
	@endif
</div>
<div class="">
    <div id="gantt_here"></div>
</div>
@endsection

@section('scripts')
<!-- Gantt files -->
<script type="text/javascript">
	var sprint = {{$sprint['id'];}};
	var comision = {{$comision;}};
	var current_user_id = {{Auth::user()->id}};
	var token_owner = {{(isset($token_owner)) ? $token_owner : 0}};
	var noEditable = {{($sprint['entregado'] || !isset($token_owner) || !$token_owner || $sprint['iniciado'] == 0 || Auth::user()->isProfesor()) ? 'true' : 'false'}};
</script>
<script src="{{ asset('dhtmlxgantt.js') }}" ></script>
<script src="{{ asset('js/gantt/main.js') }}" ></script>
@endsection
