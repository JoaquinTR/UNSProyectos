@extends('dashboard.profile')

@section('seleccion-perfil')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    Modificar datos personales
                    <a type="button" class="float-end btn-close btn-close-white" href="{{ route('dashboard.profile') }}"></a>
                </div>

                <div class="card-body bg-light">

                    @include('components.flash-message')

                    <form method="POST" action="{{ route('modify_data.name') }}">
                        {{ csrf_field() }}
                        <div class="form-row">
                            <div class="col-12">
                                <label for="name" class="form-label">Nombre</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="{{ Auth::user()->name }}">
                                    @if ($errors->has('name'))
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary" type="submit" name="send-nombre">Cambiar nombre</button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <form method="POST" action="{{ route('modify_data.alias') }}">
                        {{ csrf_field() }}
                        <div class="form-row">
                            <div class="col-12">
                                <label for="alias" class="form-label">Alias</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person-badge-fill" viewBox="0 0 16 16">
                                                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm4.5 0a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zM8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm5 2.755C12.146 12.825 10.623 12 8 12s-4.146.826-5 1.755V14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-.245z"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <input type="text" id="alias" name="alias" class="form-control" placeholder="{{ (isset(Auth::user()->alias)) ? Auth::user()->alias : '' }}">
                                    @if ($errors->has('alias'))
                                        <span class="text-danger">{{ $errors->first('alias') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary" type="submit" name="send-alias">Cambiar alias</button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <form method="POST" action="{{ route('modify_data.email') }}">
                        {{ csrf_field() }}
                        <div class="form-row">
                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                                                <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <input id="email" type="text" name="email" class="form-control" placeholder="{{ Auth::user()->email }}">
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary" type="submit" name="send-mail">Cambiar mail</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@endsection