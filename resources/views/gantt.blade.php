@extends('layouts.app')

@section('css')
<!-- <link href="dhtmlxgantt.css"rel="s'tylesheet"> -->
<link href="{{ asset('css/dhtmlx-skins/'.$css_skin.'.css') }}" rel="stylesheet">
<link href="{{ asset('css/gantt/main.css') }}" rel="stylesheet">
@endsection

@section('content')
<!-- Gantt container -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-1">
	@if(!Auth::user()->isProfesor())
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group mx-2">
			<button type="button" class="btn btn-sm btn-primary">TODO</button>
			<button type="button" class="btn btn-sm btn-primary">TODO</button>
		</div>
		<button id="token-dropdown" type="button" class="btn btn-sm btn-primary primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-activity" viewBox="0 0 16 16">
				<path fill-rule="evenodd" d="M6 2a.5.5 0 0 1 .47.33L10 12.036l1.53-4.208A.5.5 0 0 1 12 7.5h3.5a.5.5 0 0 1 0 1h-3.15l-1.88 5.17a.5.5 0 0 1-.94 0L6 3.964 4.47 8.171A.5.5 0 0 1 4 8.5H.5a.5.5 0 0 1 0-1h3.15l1.88-5.17A.5.5 0 0 1 6 2Z"/>
			</svg>
			Token
		</button>
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
	var token_owner = {{(isset($token_owner)) ? $token_owner : 0}};
	var noEditable = {{($sprint['entregado'] || !isset($token_owner) || !$token_owner || $sprint['iniciado'] == 0 || Auth::user()->isProfesor()) ? 'true' : 'false'}};
</script>
<script src="{{ asset('dhtmlxgantt.js') }}" ></script>
<script src="{{ asset('js/gantt/main.js') }}" ></script>
@endsection
