@extends('layouts.app')

@section('css')
<!-- <link href="dhtmlxgantt.css"rel="stylesheet"> -->
<link href="{{ asset('css/dhtmlx-skins/dhtmlxgantt_broadway.css') }}" rel="stylesheet">
<link href="{{ asset('css/gantt/main.css') }}" rel="stylesheet">
@endsection

@section('content')
<!-- Gantt container -->
<h1 class="display-6 text-center">Sprint #{{$sprint['numero']}}</h1>
<h4 class="mt-0 fw-bold mb-2 mx-4 {{$text_color}}">Deadline: {{$sprint['deadline'];}} {{($date_diff  >= 0) ? " - Faltan ".$date_diff." días.": " - Sprint entregado"}}</h4>
@if(!$sprint['iniciado'])
	<h4 class="mt-0 fw-bold mb-2 mx-4 text-primary">Comienza el día {{$sprint['comienzo'];}}</h4>
@endif
<div class="">
    <div id="gantt_here"></div>
</div>
@endsection

@section('scripts')
<!-- Gantt files -->
<script type="text/javascript">
	var sprint = {{$sprint['id'];}};
	var comision = {{$comision;}};
	var noEditable = {{($sprint['entregado'] || $sprint['iniciado'] == 0 || Auth::user()->isProfesor()) ? 'true' : 'false'}};
</script>
<script src="{{ asset('dhtmlxgantt.js') }}" ></script>
<script src="{{ asset('js/gantt/main.js') }}" ></script>
@endsection
