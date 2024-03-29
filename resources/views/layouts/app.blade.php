<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Cedo al css custom -->
    @yield('css')

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

    <!-- Alertas (mutar entre los tipos posibles formando array y no variable simple) -->
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @auth
    <div id="offcanvasMenuAcciones" class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1" aria-labelledby="offcanvasMenuAccionesLabel" style="width: 5rem;">
        <div class="offcanvas-header bg-dark">
            <!-- <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button> -->
            <button id="menu-acciones" type="button" class="btn" data-bs-toggle="offcanvas" role="button" data-bs-target="#offcanvasMenuAcciones" aria-controls="offcanvasMenuAcciones" >
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi bi-caret-left-square" viewBox="0 0 16 16">
                <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                <path d="M10.205 12.456A.5.5 0 0 0 10.5 12V4a.5.5 0 0 0-.832-.374l-4.5 4a.5.5 0 0 0 0 .748l4.5 4a.5.5 0 0 0 .537.082z"/>
            </svg>
                <span class="visually-hidden">Button</span>
            </button>
        </div>
        <div class="offcanvas-body bg-dark p-0">
              <div class="d-flex flex-column flex-shrink-0 bg-dark" style="">
                <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
                    <li class="nav-item">
                        <a href="{{ route('gantt') }}" class="nav-link {{ (Request::is('gantt*') ) ? 'active' : '' }} py-3 " aria-current="page" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Tablero Gantt" data-bs-trigger="hover">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-bar-chart-steps" viewBox="0 0 16 16">
                                <path d="M.5 0a.5.5 0 0 1 .5.5v15a.5.5 0 0 1-1 0V.5A.5.5 0 0 1 .5 0zM2 1.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5v-1zm2 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-1zm2 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1-.5-.5v-1zm2 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-1z"/>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}" class="nav-link {{ (Request::is('dashboard')) ? 'active' : '' }} py-3" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Dashboard" data-bs-trigger="hover">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-speedometer" viewBox="0 0 16 16">
                                <path d="M8 2a.5.5 0 0 1 .5.5V4a.5.5 0 0 1-1 0V2.5A.5.5 0 0 1 8 2zM3.732 3.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM2 8a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8zm9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5zm.754-4.246a.389.389 0 0 0-.527-.02L7.547 7.31A.91.91 0 1 0 8.85 8.569l3.434-4.297a.389.389 0 0 0-.029-.518z"/>
                                <path fill-rule="evenodd" d="M6.664 15.889A8 8 0 1 1 9.336.11a8 8 0 0 1-2.672 15.78zm-4.665-4.283A11.945 11.945 0 0 1 8 10c2.186 0 4.236.585 6.001 1.606a7 7 0 1 0-12.002 0z"/>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.profile') }}" class="nav-link {{ (Request::is('dashboard/profile') ) ? 'active' : '' }} py-3" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Perfil" data-bs-trigger="hover">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-fingerprint" viewBox="0 0 16 16">
                                <path d="M8.06 6.5a.5.5 0 0 1 .5.5v.776a11.5 11.5 0 0 1-.552 3.519l-1.331 4.14a.5.5 0 0 1-.952-.305l1.33-4.141a10.5 10.5 0 0 0 .504-3.213V7a.5.5 0 0 1 .5-.5Z"/>
                                <path d="M6.06 7a2 2 0 1 1 4 0 .5.5 0 1 1-1 0 1 1 0 1 0-2 0v.332c0 .409-.022.816-.066 1.221A.5.5 0 0 1 6 8.447c.04-.37.06-.742.06-1.115V7Zm3.509 1a.5.5 0 0 1 .487.513 11.5 11.5 0 0 1-.587 3.339l-1.266 3.8a.5.5 0 0 1-.949-.317l1.267-3.8a10.5 10.5 0 0 0 .535-3.048A.5.5 0 0 1 9.569 8Zm-3.356 2.115a.5.5 0 0 1 .33.626L5.24 14.939a.5.5 0 1 1-.955-.296l1.303-4.199a.5.5 0 0 1 .625-.329Z"/>
                                <path d="M4.759 5.833A3.501 3.501 0 0 1 11.559 7a.5.5 0 0 1-1 0 2.5 2.5 0 0 0-4.857-.833.5.5 0 1 1-.943-.334Zm.3 1.67a.5.5 0 0 1 .449.546 10.72 10.72 0 0 1-.4 2.031l-1.222 4.072a.5.5 0 1 1-.958-.287L4.15 9.793a9.72 9.72 0 0 0 .363-1.842.5.5 0 0 1 .546-.449Zm6 .647a.5.5 0 0 1 .5.5c0 1.28-.213 2.552-.632 3.762l-1.09 3.145a.5.5 0 0 1-.944-.327l1.089-3.145c.382-1.105.578-2.266.578-3.435a.5.5 0 0 1 .5-.5Z"/>
                                <path d="M3.902 4.222a4.996 4.996 0 0 1 5.202-2.113.5.5 0 0 1-.208.979 3.996 3.996 0 0 0-4.163 1.69.5.5 0 0 1-.831-.556Zm6.72-.955a.5.5 0 0 1 .705-.052A4.99 4.99 0 0 1 13.059 7v1.5a.5.5 0 1 1-1 0V7a3.99 3.99 0 0 0-1.386-3.028.5.5 0 0 1-.051-.705ZM3.68 5.842a.5.5 0 0 1 .422.568c-.029.192-.044.39-.044.59 0 .71-.1 1.417-.298 2.1l-1.14 3.923a.5.5 0 1 1-.96-.279L2.8 8.821A6.531 6.531 0 0 0 3.058 7c0-.25.019-.496.054-.736a.5.5 0 0 1 .568-.422Zm8.882 3.66a.5.5 0 0 1 .456.54c-.084 1-.298 1.986-.64 2.934l-.744 2.068a.5.5 0 0 1-.941-.338l.745-2.07a10.51 10.51 0 0 0 .584-2.678.5.5 0 0 1 .54-.456Z"/>
                                <path d="M4.81 1.37A6.5 6.5 0 0 1 14.56 7a.5.5 0 1 1-1 0 5.5 5.5 0 0 0-8.25-4.765.5.5 0 0 1-.5-.865Zm-.89 1.257a.5.5 0 0 1 .04.706A5.478 5.478 0 0 0 2.56 7a.5.5 0 0 1-1 0c0-1.664.626-3.184 1.655-4.333a.5.5 0 0 1 .706-.04ZM1.915 8.02a.5.5 0 0 1 .346.616l-.779 2.767a.5.5 0 1 1-.962-.27l.778-2.767a.5.5 0 0 1 .617-.346Zm12.15.481a.5.5 0 0 1 .49.51c-.03 1.499-.161 3.025-.727 4.533l-.07.187a.5.5 0 0 1-.936-.351l.07-.187c.506-1.35.634-2.74.663-4.202a.5.5 0 0 1 .51-.49Z"/>
                            </svg>
                        </a>
                    </li>
                    @if(Auth::user()->isProfesor())
                    <li>
                        <!-- Solo si es profesor -->
                        <a href="{{ route('admin') }}" class="nav-link {{ (Request::is('admin*')) ? 'active' : '' }} py-3" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Panel de control" data-bs-trigger="hover">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-clipboard2-data" viewBox="0 0 16 16">
                            <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5h3Z"/>
                            <path d="M3 2.5a.5.5 0 0 1 .5-.5H4a.5.5 0 0 0 0-1h-.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1H12a.5.5 0 0 0 0 1h.5a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-12Z"/>
                            <path d="M10 7a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7Zm-6 4a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1Zm4-3a1 1 0 0 0-1 1v3a1 1 0 1 0 2 0V9a1 1 0 0 0-1-1Z"/>
                        </svg>
                        </a>
                        <!-- Fin solo profesor -->
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @endauth

    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm" style="height: 4rem !important;">
                @auth
                <button id="menu-acciones" type="button" class="btn mx-4" data-bs-toggle="offcanvas" role="button" data-bs-target="#offcanvasMenuAcciones" aria-controls="offcanvasMenuAcciones" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi bi-caret-right-square" viewBox="0 0 16 16">
                        <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"></path>
                        <path d="M5.795 12.456A.5.5 0 0 1 5.5 12V4a.5.5 0 0 1 .832-.374l4.5 4a.5.5 0 0 1 0 .748l-4.5 4a.5.5 0 0 1-.537.082z"></path>
                    </svg>
                    <span class="visually-hidden">Button</span>
                </button>
                @endauth
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img class="" src="{{ asset('imagenes/dcic.png') }}" alt="UNSP" width="35" height="35">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="ms-5">
                    <div id="compañeros" class="row">
                    @if(isset($token_owner) && isset($compañeros))
                        @foreach ($compañeros as $idx => $compañero)
                        <div class="col">
                            <div class='icon-container'
                            data-bs-toggle="profile-info"
                            data-bs-placement="bottom"
                            data-bs-original-title="<h5 class='m-0 fw-bold'>{{(isset($compañero['alias'])) ? $compañero['alias'] : $compañero['nombre']}}</h5>"
                            data-bs-content="
                            <img src='https://cdn2.iconfinder.com/data/icons/flatfaces-everyday-people-square/128/beard_male_man_face_avatar-512.png' class='card-img-top'>
                            <ul class='list-group list-group-flush  text-center'>
                                <li class='list-group-item text-wrap'><h5><span class='fw-bold'>Nombre:</span><br> {{$compañero['nombre']}}</h5></li>
                                <li class='list-group-item text-wrap'><h5><span class='fw-bold'>Email:</span><br> {{$compañero['email']}}</h5></li>
                                <li id='token-msg-{{$compañero['id']}}' class='list-group-item text-wrap d-none'><h5><span class='fw-bold'>Permisos de edición otorgados</span></h5></li>
                            </ul>"
                            >
                                <img id="imagen-{{$compañero['id']}}" src="https://cdn2.iconfinder.com/data/icons/flatfaces-everyday-people-square/128/beard_male_man_face_avatar-512.png" class="rounded-circle mx-2 {{(isset($compañero['last_seen']) && $compañero['last_seen'] != 'gone') ? 'border border-3 border-success' : ''}}"  width="40" height="40" >
                                <div id="status-{{$compañero['id']}}" class='status-circle {{(isset($compañero['last_seen']) && $compañero['last_seen'] != 'gone') ? '' : 'gone'}}'></div>
                                <div id="token-{{$compañero['id']}}" class='token-circle d-none'>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#0d6efd" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                    </div>
                </div>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ (!Auth::user()->isProfesor() && Auth::user()->alias != "") ? Auth::user()->alias : Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="">
            <!-- Contenido interno del body -->
            @yield('content')
        </main>
    </div>
    
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/dashboard/main.js') }}" defer></script>
    <!-- Cedo a los scripts custom -->
    @yield('scripts')
</body>
</html>
