@extends('layouts.app')

@section('css')
@endsection

@section('content')
<div class="ms-5 mt-3 d-flex flex-row">
        <div class="justify-content-left w-20">
            <div class="card">
                <div class="card-header bg-dark">
                   <h4 class="text-white mb-0">Perfil de usuario</h4>
                </div>
                <div class="">
                    <div class="d-grid gap-2 mx-3 mt-2">
                        <div class="">
                            <h5><strong>Nombre:</strong></h5>
                        </div>
                        <div class="text-center">
                            {{ Auth::user()->name }}
                        </div>
                        @if(isset(Auth::user()->alias))
                            <div class="">
                                <h5><strong>Alias:</strong></h5>
                            </div>
                            <div class="text-center">
                                {{ Auth::user()->alias }}
                            </div>
                        @endif
                        <div class="">
                            <h5><strong>E-mail:</strong></h5>
                        </div>
                        <div class="text-center">
                            {{ Auth::user()->email }}
                        </div>
                    </div>

                    <hr/>

                    <div class="d-grid gap-2 mx-3 mb-3">
                        <a class="btn btn-primary" href="{{ route('modify_data') }}">
                            Modificar datos
                        </a>
                        <a class="btn btn-primary btn-block" href="{{ route('modify_passw') }}">
                            Cambiar contraseña
                        </a>
                        <a class="btn btn-primary  btn-block" href="{{ route('password.request') }}">
                            Resetear contraseña
                        </a>
                    </div>

                    <hr/>

                    <div class="d-grid gap-2 mx-3 mb-3">
                        <a class="btn btn-primary" href="{{ route('settings') }}">
                            Aspecto gantt
                        </a>
                    </div>

                </div>
            </div>
        </div>
        <div class="flex-fill">
            @yield('seleccion-perfil')
        </div>
    </div>
@endsection

@section('scripts')
@endsection