@extends('layouts.app')

@section('css')
<link href="{{ asset('css/dashboard/main.css') }}" rel="stylesheet">
@endsection

@section('content')
@if(!empty($comisiones) && $comisiones->count())
@foreach ($comisiones as $idx => $comision)
<div class="container py-5 custom-bg" style="height: 100%">
    <div class="row text-center text-dark mb-2">
        <div class="col-lg-7 mx-auto">
            <h1 class="display-6">{{$comision->nombre}}</h1>
        </div>
    </div>
    <!-- Construyo el dashboard -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- List group-->
            <ul class="list-group shadow">
                <!-- list group item-->
                @foreach ($comision->sprints()->get() as $idx => $sprint)
                <a href="{{ route('gantt.view',['sprint_id'=>$sprint->id]) }}" class="text-decoration-none">
                    <li class="list-group-item">
                        <!-- Sprint -->
                        <div class="media align-items-lg-center flex-column flex-lg-row p-3">
                            <div class="media-body order-2 order-lg-1">
                                <h5 class="mt-0 font-weight-bold mb-2">Sprint #{{$sprint->numero}}</h5>
                                @if($sprint->iniciado)
                                    @if(!$sprint->entregado)
                                        <p class="font-italic fw-bold text-success mb-0 medium">En curso</p> <!-- TODO: Hacer que su presencia dependa de laravel y que marque con color el div padre -->
                                        <p class="font-italic mb-0 medium">Deadline: {{$sprint->deadline}}</p>
                                    @else
                                        <p class="font-italic fw-bold text-danger mb-0 medium">Entregado</p> <!-- TODO: Hacer que su presencia dependa de laravel y que marque con color el div padre -->
                                        <p class="font-italic mb-0 medium">Nota: {{($sprint->nota_final) ? $sprint->nota_final : 'pendiente a corrección'}}</p> <!-- TODO: Hacer que su presencia dependa de laravel y que marque con color el div padre -->
                                    @endif
                                @else
                                    <p class="font-italic fw-bold text-primary mb-0 medium">Comienza el: {{$sprint->comienzo}}</p>
                                @endif
                            </div>
                        </div> <!-- End -->
                    </li> <!-- End -->
                </a>
                @endforeach
            </ul> <!-- End -->
        </div>
    </div>
</div>
@endforeach
@else
    <div class="row text-center text-dark mb-5">
        <div class="col-lg-7 mx-auto">
            <h1 class="display-6">Aún no comenzó la materia</h1>
        </div>
    </div>
@endif
@endsection

@section('scripts')
@endsection