@extends('dashboard.profile')

@section('seleccion-perfil')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Modificar contraseña
                    <a type="button" class="float-end btn-close btn-close-dark" href="{{ route('dashboard.profile') }}"></a>
                </div>

                <div class="card-body bg-light">

                    @include('components.flash-message')

                    <form method="POST" id="login" autocomplete="off" class="bg-light" action="{{ url('/dashboard/profile/contraseña') }}">
                        {{ csrf_field() }}
                        <div class="form-row">
                            <div class="col-12">
                                <div class="input-group mb-3">
                                    <input name="password" autocomplete="off" type="password" value="" class="input form-control" id="password" placeholder="nueva contraseña" required="true" aria-label="password" aria-describedby="basic-addon1" readonly onfocus="this.removeAttribute('readonly');"/>
                                    <div class="input-group-append">
                                        <span id="password_eye" class="input-group-text">
                                        <svg id="show_eye" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                        </svg>
                                        <svg id="hide_eye" class="d-none" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                            <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                            <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/>
                                        </svg>
                                        </span>
                                    </div>
                                    @if ($errors->has('password'))
                                    <div class="col-12">
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group mb-3">
                                    <input name="passwordr" autocomplete="off" type="password" value="" class="input form-control" id="passwordr" placeholder="repetir nueva contraseña" required="true" aria-label="password" aria-describedby="basic-addon1" readonly onfocus="this.removeAttribute('readonly');"/>
                                    <div class="input-group-append">
                                        <span id="passwordr_eye" class="input-group-text">
                                            <svg id="show_eye" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                            </svg>
                                            <svg id="hide_eye" class="d-none" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                                <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/>
                                            </svg>
                                        </span>
                                    </div>
                                    @if ($errors->has('passwordr'))
                                    <div class="col-12">
                                        <span class="text-danger">{{ $errors->first('passwordr') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary" type="submit" name="signin">Finalizar</button>
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
<script src="{{ asset('js/profile/password-reset.js') }}" defer></script
@endsection